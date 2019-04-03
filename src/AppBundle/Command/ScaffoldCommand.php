<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use AppBundle\Common\StringToolkit;
use Symfony\Component\Filesystem\Filesystem;

class ScaffoldCommand extends BaseCommand
{
    protected $mode;
    protected $names;
    protected $paths;

    protected function configure()
    {
        $this
            ->setName('biz:scaffold')
            ->setDescription('创建脚手架')
            ->addArgument('tableName', InputArgument::REQUIRED, 'table_name, example: user, user_profile')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'module_name, example: User')
            ->addArgument('mode', InputArgument::REQUIRED, 'DSC: D=dao,S=service,C=Controller');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>创建脚手架...</info>');
        $this->initInputs($input);
        $this->initPaths();

        if (strpos($this->mode, 'D') !== false) {
            $this->createDaoTemplate($output);
            $output->writeln('<info>Dao创建完成</info>');
        }
        if (strpos($this->mode, 'S') !== false) {
            $this->createServiceTemplate($output);
            $output->writeln('<info>Service创建完成</info>');
        }
        if (strpos($this->mode, 'C') !== false) {
            $this->createControllerTemplate($output);
            $output->writeln('<info>Controller创建完成</info>');
        }

        $output->writeln('<info>脚手架创建完毕</info>');
    }

    protected function initInputs(InputInterface $input)
    {
        $this->mode = $input->getArgument('mode');

        $tableName = $input->getArgument('tableName');
        $moduleName = $input->getArgument('moduleName');

        $smallName = StringToolkit::toCamelCase($tableName);
        $bigName = ucfirst($smallName);

        $smallPluralName = $this->simplePluralize($smallName);
        $bigPluralName = $this->simplePluralize($bigName);

        $underscoreName = StringToolkit::toUnderScore($smallName);
        $underscorePluralName = $this->simplePluralize($underscoreName);
        $dashCaseName = $this->underscoreNameToDashCase($underscoreName);

        //table fields
        $biz = $this->getBiz();
        $tableInfo = $biz['db']->fetchAll("desc {$tableName}");
        $daoConditionFields = '';
        $timestampFields = '';
        $tableFields = '';
        $tableFieldsWithDefaultValue = '';
        foreach ($tableInfo as $fieldInfo) {
            if ($fieldInfo['Field'] == 'id') {
                continue;
            }

            $daoConditionFields .= "'{$fieldInfo['Field']} = :{$fieldInfo['Field']}', \n";

            // timestampFields只用在daoImpl中，service中不需要
            if (in_array($fieldInfo['Field'], array('created_time', 'updated_time'))) {
                $timestampFields .= "'{$fieldInfo['Field']}', \n";
                continue;
            }

            $tableFields .= "'{$fieldInfo['Field']}', \n";

            if (strpos($fieldInfo['Type'], 'int') === 0) {
                $defaultValue = empty($fieldInfo['Default']) ? 0 : $fieldInfo['Default'];
            } else {
                $defaultValue = empty($fieldInfo['Default']) ? "''" : "'{$fieldInfo['Default']}'";
            }
            $tableFieldsWithDefaultValue .= "'{$fieldInfo['Field']}' => {$defaultValue}, \n";
        }

        $this->names = array(
            'tableName' => $tableName,
            'moduleName' => $moduleName,
            'smallName' => $smallName,
            'bigName' => $bigName,
            'smallPluralName' => $smallPluralName,
            'underscorePluralName' => $underscorePluralName,
            'bigPluralName' => $bigPluralName,
            'dashCaseName' => $dashCaseName,
            'underscoreName' => $underscoreName,
            'daoConditionFields' => $daoConditionFields,
            'tableFields' => $tableFields,
            'tableFieldsWithDefaultValue' => $tableFieldsWithDefaultValue,
            'timestampFields' => $timestampFields,
        );
    }

    protected function initPaths()
    {
        $rootDirectory = realpath($this->getContainer()->getParameter('kernel.root_dir').'/../');

        $tpl = $rootDirectory.'/src/AppBundle/Command/Template';
        $dao = $rootDirectory.'/src/Biz/'.$this->names['moduleName'].'/Dao';
        $service = $rootDirectory.'/src/Biz/'.$this->names['moduleName'].'/Service';
        $testService = $rootDirectory.'/tests/'.$this->names['moduleName'].'/Service';
        $controller = $rootDirectory.'/src/AppBundle/Controller/Admin';

        $this->paths = array(
            'tpl' => $tpl,
            'dao' => $dao,
            'dao_impl' => $dao.'/Impl',
            'service' => $service,
            'service_impl' => $service.'/Impl',
            'test_service' => $testService,
            'controller' => $controller,
        );
    }

    protected function createDaoTemplate($output)
    {
        $filesystem = new Filesystem();
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(array($this->paths['tpl'])));

        $filesystem->dumpFile("{$this->paths['dao']}/{$this->names['bigName']}Dao.php", $twig->render('Dao.twig', $this->names), 0777);
        $filesystem->dumpFile("{$this->paths['dao_impl']}/{$this->names['bigName']}DaoImpl.php", $twig->render('DaoImpl.twig', $this->names), 0777);
    }

    protected function createServiceTemplate($output)
    {
        $filesystem = new Filesystem();
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(array($this->paths['tpl'])));

        $filesystem->dumpFile("{$this->paths['service']}/{$this->names['bigName']}Service.php", $twig->render('Service.twig', $this->names), 0777);
        $filesystem->dumpFile("{$this->paths['service_impl']}/{$this->names['bigName']}ServiceImpl.php", $twig->render('ServiceImpl.twig', $this->names), 0777);
        $filesystem->dumpFile("{$this->paths['test_service']}/{$this->names['bigName']}ServiceTest.php", $twig->render('ServiceTest.twig', $this->names), 0777);
    }

    protected function createControllerTemplate($output)
    {
        $filesystem = new Filesystem();
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem(array($this->paths['tpl'])));

        $filesystem->dumpFile("{$this->paths['controller']}/{$this->names['bigName']}Controller.php", $twig->render('AdminController.twig', $this->names), 0777);
    }

    protected function simplePluralize($singular)
    {
        $lastLetter = strtolower($singular[strlen($singular) - 1]);
        $lastLetter2 = strtolower($singular[strlen($singular) - 2]);

        if (in_array($lastLetter, array('s', 'x')) || in_array($lastLetter.$lastLetter2, array('sh', 'es'))) {
            return $singular.'es';
        } elseif ($lastLetter == 'y' && in_array($lastLetter2, array('a', 'e', 'i', 'o', 'u'))) {
            return substr($singular, 0, -1).'ies';
        } else {
            return $singular.'s';
        }
    }

    protected function underscoreNameToDashCase($underscoreName)
    {
        return str_replace('_', '-', $underscoreName);
    }

}
