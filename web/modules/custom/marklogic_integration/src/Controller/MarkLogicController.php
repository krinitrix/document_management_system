<?php
namespace Drupal\marklogic_integration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\marklogic_integration\Service\MarkLogicClient;
use Symfony\Component\DependencyInjection\ContainerInterface;


class MarkLogicController extends ControllerBase{

    public function status(){
        return [
            '#type'=>'markup',
            '#markup'=>'MarkLogic Integration module is working !!! '
        ];
    }

    protected MarkLogicClient $markLogicClient;
    public function __construct(MarkLogicClient $marklogic_client) {
        $this->markLogicClient = $marklogic_client;
  }
    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('marklogic_integration.client')
    );
  }
      public function saveEmployee() {

    $employee = [
      'employeeId' => 'EMP002',
      'name' => 'Alice Smith',
      'email' => 'alice@example.com',
      'department' => 'Engineering',
      'designation' => 'Backend Developer',
      'salary' => 50000,
    ];

    $uri = '/employees/EMP002.json';

    try {
      $result = $this->markLogicClient->saveDocument($uri, $employee);

      $this->messenger()->addStatus(
        $this->t('Employee document saved successfully in MarkLogic. HTTP status: @status', [
          '@status' => $result['status'],
        ])
      );

    }
    catch (\Throwable $e) {
      $this->getLogger('marklogic_integration')->error(
        'MarkLogic error: @message',
        ['@message' => $e->getMessage()]
      );

      $this->messenger()->addError(
        $this->t('Unable to save employee document. Check the Drupal logs.')
      );
    }

    return [
      '#type' => 'markup',
      '#markup' => $this->t('MarkLogic employee insertion test.'),
    ];
  }



}






