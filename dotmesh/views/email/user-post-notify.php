<?php defined('DOTMESH_ROOT_DIR') || die ?>
<?php $u = $this->user; $n = DotMesh_Model_Node::i()->localNode() ?>
<!--{ To: "<?=$u->firstname.' '.$u->lastname?>" <<?=$u->email?>> }-->
<!--{ From: "DotMesh (<?=$n->uri()?>)" <<?=$n->support_email?>> }-->
<!--{ Subject: New Post Notification }-->

Hello <?=$u->firstname?>,
