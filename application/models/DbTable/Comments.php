<?php

class Application_Model_DbTable_Comments extends Zend_Db_Table_Abstract
{

    protected $_name = 'comments';

    protected $_referenceMap    = array(
        'CommentsActivities' => array(
            'columns'           => 'activity_id',
            'refTableClass'     => 'Application_Model_DbTable_Activities',
            'refColumns'        => 'id'
        ),
    );

    public function getCommentsWithUserByActivity($activityId) {
        $comments = $this->fetchAll('activity_id =' . $activityId);
        $fullComments = array();
        $i = 0;
        foreach ($comments as $comment) {
            $user = $comment->findDependentRowset('Application_Model_DbTable_Users');
            $fullComments[$i]['comment'] = $comment;
            $fullComments[$i]['user'] = $user;
            $i++;
        }

        return $fullComments;
    }

}

