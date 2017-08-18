<?php

namespace Anthony\Hsing\Model\Dao;

use Anthony\Hsing\Model\Db\Project;

class ProjectDao extends \Phalcon\Mvc\User\Component
{

    public function add($projectData)
    {
        $projectObj = new Project();

        $projectObj->name        = $projectData['name'];
        $projectObj->owner       = $projectData['owner'];
        $projectObj->status      = $projectData['status'];
        $projectObj->memberLimit = $projectData['memberLimit'];
        $projectObj->createTime  = date('Y-m-d H:i:s');
        $projectObj->updateTime  = date('Y-m-d H:i:s');

        if (!$projectObj->save()) {
            echo $projectObj->getMessage();
        }

        $projectId = $projectObj->projectId;

        return $projectId;
    }

    public function delete($projectId)
    {
        $projectObj = Project::findFirst(
            [
             "conditions" => "projectId = ?1",
             "bind"       => [1 => $projectId],
            ]
        );

        if (!$projectObj->delete()) {
            throw new ProjectException("10107");
        }
    }

    public function update($projectId, $projectData)
    {
        $projectObj = Project::findFirst(
            [
             "conditions" => "projectId = ?1",
             "bind"       => [1 => $projectId],
            ]
        );

        $projectObj->name        = $projectData['name'];
        $projectObj->owner       = $projectData['owner'];
        $projectObj->status      = $projectData['status'];
        $projectObj->memberLimit = $projectData['memberLimit'];
        $projectObj->updateTime  = date('Y-m-d H:i:s');

        if (!$projectObj->save()) {
            throw new ProjectException("10105");
        }
    }

    public function get($projectId)
    {
        $projectObj = Project::findFirst(
            [
             "conditions" => "projectId = ?1",
             "bind"       => [1 => $projectId],
            ]
        );

        return $projectObj;
    }

    public function getList()
    {
        $projectObj = Project::find();
        return $projectObj;
    }
}