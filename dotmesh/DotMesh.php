<?php

BModule::defaultRunLevel(BModule::REQUESTED);

BModuleRegistry::i()->addModule('DotMesh', array(
    'version' => '0.1.0',
    'bootstrap' => array('callback'=>'DotMesh::bootstrap'),
    'migrate' => 'DotMesh_Migrate',
));

class DotMesh extends BClass
{
    public static function bootstrap()
    {
        BApp::m()->autoload();
        
        BFrontController::i()
            ->route('_ /noroute', 'DotMesh_Controller_Nodes.noroute', array(), null, false)
            
            ->route('GET /', 'DotMesh_Controller_Accounts.home')
            ->route('GET /:term', 'DotMesh_Controller_Nodes.catch_all')

            ->route('GET|POST /a/.action', 'DotMesh_Controller_Accounts')
            ->route('GET|POST /n/.action', 'DotMesh_Controller_Nodes')
            
            ->route('GET|POST|PUT|HEAD|OPTIONS /u/:username', 'DotMesh_Controller_Users.index')
            ->route('GET|POST|PUT|HEAD|OPTIONS /p/:postname', 'DotMesh_Controller_Posts.index')
            ->route('GET|POST|PUT|HEAD|OPTIONS /t/:tagname', 'DotMesh_Controller_Tags.index')
            
            ->route('GET|POST /p/:postname/reply', 'DotMesh_Controller_Posts.reply')

            ->route('^(GET|POST|PUT|HEAD|OPTIONS) /u/([a-zA-Z0-9_]+)/api1\.json$', 'DotMesh_Controller_Users.api1_json')
            ->route('^(GET|POST|PUT|HEAD|OPTIONS) /p/([a-zA-Z0-9_]+)/api1\.json$', 'DotMesh_Controller_Posts.api1_json')
            ->route('^(GET|POST|PUT|HEAD|OPTIONS) /t/([a-zA-Z0-9_]+)/api1\.json$', 'DotMesh_Controller_Tags.api1_json')

            ->route('^GET /u/([a-zA-Z0-9_]+)/feed\.rss$', 'DotMesh_Controller_Users.feed_rss')
            ->route('^GET /p/([a-zA-Z0-9_]+)/feed\.rss$', 'DotMesh_Controller_Posts.feed_rss')
            ->route('^GET /t/([a-zA-Z0-9_]+)/feed\.rss$', 'DotMesh_Controller_Tags.feed_rss')

            ->route('^GET /u/([a-zA-Z0-9_]+)/thumb\.(png|jpg|gif)$', 'DotMesh_Controller_Users.thumb')
        ;

        BLayout::i()
            ->addView('head', array('view_class'=>'BViewHead'))
            ->addAllViews('views')

            ->addLayout(array(
                'base' => array(
                    array('hook', 'head', 'views'=>array('head')),
                    array('hook', 'header', 'views'=>array('header')),
                    array('hook', 'footer', 'views'=>array('footer')),
                    array('view', 'head', 'do'=>array(
                        array('css', '{DotMesh}/css/normalize.css'),
                        array('css', '{DotMesh}/css/main.css'),
                        array('css', '{DotMesh}/css/dotmesh.css'),
                        array('js', '{DotMesh}/js/head.min.js'),
                        array('js', '{DotMesh}/js/es5-shim.min.js'),
                        array('js', '//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js'),
                        //array('js', '{DotMesh}/js/jquery.min.js'),
                        array('js', '{DotMesh}/js/dotmesh.js'),
                    )),
                ),
                '404' => array(
                    array('layout', 'base'),
                    array('hook', 'main', 'views'=>array('404')),
                ),
                'xhr-timeline' => array(
                    array('root', 'timeline'),
                ),
                '/' => array(
                    array('layout', 'base'),
                    array('hook', 'main', 'views'=>array('home')),
                ),
                '/public' => array(
                    array('layout', 'base'),
                    array('hook', 'main', 'views'=>array('public')),
                ),
                '/setup' => array(
                    array('layout', 'base'),
                    array('hook', 'main', 'views'=>array('setup')),
                ),
                '/signup' => array(
                    array('layout', 'base'),
                    array('hook', 'main', 'views'=>array('signup')),
                ),
                '/account' => array(
                    array('layout', 'base'),
                    array('hook', 'main', 'views'=>array('account')),
                ),
                '/search' => array(
                    array('layout', 'base'),
                    array('hook', 'main', 'views'=>array('search')),
                ),
                '/thread' => array(
                    array('layout', 'base'),
                    array('hook', 'main', 'views'=>array('thread')),
                ),
                '/user' => array(
                    array('layout', 'base'),
                    array('hook', 'main', 'views'=>array('user')),
                ),
                '/tag' => array(
                    array('layout', 'base'),
                    array('hook', 'main', 'views'=>array('tag')),
                ),
            ));
        ;
    }
}

/************************************************************************/

class DotMesh_Controler_Abstract extends BActionController
{
    public function beforeDispatch()
    {
        if (!parent::beforeDispatch()) {
            return false;
        }
        $r = BRequest::i();
        if (!DotMesh_Model_Node::i()->localNode() && $r->rawPath()!=='/a/setup') {
            BResponse::i()->redirect(BApp::href('a/setup'));
        }
        if (($guest = $r->get('guest_uri'))) {
            DotMesh_Model_User::i()->acceptGuest($guest, $r->get('guest_signature'));
        }
        return true;
    }

    public function afterDispatch()
    {
        parent::afterDispatch();
        BResponse::i()->output();
    }
}

/************************************************************************/

class DotMesh_Model_UserSub extends BModel
{
    protected static $_origClass = __CLASS__;
    protected static $_table = 'dm_user_sub';
}

class DotMesh_Model_TagSub extends BModel
{
    protected static $_origClass = __CLASS__;
    protected static $_table = 'dm_tag_sub';
}

class DotMesh_Model_PostFeedback extends BModel
{
    protected static $_origClass = __CLASS__;
    protected static $_table = 'dm_post_feedback';
}

class DotMesh_Model_PostTag extends BModel
{
    protected static $_origClass = __CLASS__;
    protected static $_table = 'dm_post_tag';
}

class DotMesh_Model_PostUser extends BModel
{
    protected static $_origClass = __CLASS__;
    protected static $_table = 'dm_post_user';
}

class DotMesh_Model_NodeBlock extends BModel
{
    protected static $_origClass = __CLASS__;
    protected static $_table = 'dm_node_block';
}

class DotMesh_Model_UserBlock extends BModel
{
    protected static $_origClass = __CLASS__;
    protected static $_table = 'dm_user_block';
}

class DotMesh_Migrate extends BClass
{
    public static function run()
    {
        
    }
}