<?php
namespace Anthony\Hsing\Model\Service;

use Anthony\Hsing\Model\Dao\ProjectDao;
use \Exception;

class ProjectService
{

    public function addProject($projectData)
    {
        $projectData = json_decode(json_encode($projectData), true);
        $projectDao  = new ProjectDao();
        $projectId   = $projectDao->add($projectData);

        return $projectId;
    }

}