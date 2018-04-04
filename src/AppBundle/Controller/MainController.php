<?php

namespace AppBundle\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    /**
     * @var ObjectRepository
     */
    private $projectRepo;

    public function __construct(ObjectRepository $projectRepository)
    {
        $this->projectRepo = $projectRepository;
    }

    /**
     * @Route("/", name="main")
     *"@Template()
     */
    public function mainAction()
    {
        return ['projects' => $this->projectRepo->findAll()];
    }
}
