<?php

class Export_ExportersController extends Zend_Controller_Action
{
    public function addAction()
    {
        $form = new Export_Form_ExporterForm();
        $exporter = new Export_Exporter;

        if ($this->getRequest()->isPost()) {
            $this->validateAndSave($form, $exporter);
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        $db = $this->getHelper('db');

        $exporter = $db->getTable('Export_Exporter')->find($this->getParam('id'));
        $form = new Export_Form_ExporterForm(array(
            'exporter' => $exporter
        ));

        if ($this->getRequest()->isPost()) {
            $this->validateAndSave($form, $exporter);
        }

        $this->view->form = $form;
    }

    public function deleteAction()
    {
        $db = $this->getHelper('db');

        $exporter = $db->getTable('Export_Exporter')->find($this->getParam('id'));
        $form = new Export_Form_ExporterDeleteForm(array(
            'exporter' => $exporter
        ));

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                try {
                    $exporter->delete();
                    $this->flash(__('Exporter successfully deleted'));
                    $this->redirect('export');
                } catch (Exception $e) {
                    $this->flash(sprintf('Deletion of exporter failed : %s', $e->getMessage()), 'error');
                }
            }
        }

        $this->view->exporter = $exporter;
        $this->view->form = $form;
    }

    public function configureWriterAction()
    {
        $db = $this->getHelper('db');

        $exporter = $db->getTable('Export_Exporter')->find($this->getParam('id'));
        $writer = $exporter->getWriter();

        $form = $writer->getConfigForm();
        $form->addElement('submit', 'submit', array(
            'label' => __('Save'),
        ));

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $writer->handleConfigForm($form);
                $exporter->setWriterConfig($writer->getConfig());
                $exporter->save();
                $this->flash(__('Writer configuration saved'));
                $this->redirect('export');
            } else {
                $this->flash(__('Form is invalid'), 'error');
            }
        }

        $this->view->form = $form;
    }


    public function startAction()
    {
        $db = $this->getHelper('db');

        $exporter = $db->getTable('Export_Exporter')->find($this->getParam('id'));
        $writer = $exporter->getWriter();

        $session = new Zend_Session_Namespace('ExporterStartForm');
        if (!$this->getRequest()->isPost()) {
            $session->unsetAll();
        }
        if (isset($session->writer)) {
            $writer->setParams($session->writer);
        }

        $formsCallbacks = $this->getStartFormsCallbacks($exporter);
        $formCallback = reset($formsCallbacks);

        if ($this->getRequest()->isPost()) {
            $currentForm = $this->getRequest()->getPost('current_form');
            $form = call_user_func($formsCallbacks[$currentForm]);
            if ($form->isValid($_POST)) {
                $values = $form->getValues();
                $session->{$currentForm} = $values;
                if ($currentForm == 'writer') {
                    $writer->handleParamsForm($form);
                    $session->writer = $writer->getParams();
                    $formCallback = $formsCallbacks['start'];
                } elseif ($currentForm == 'start') {
                    $export = new Export_Export;
                    $export->exporter_id = $exporter->id;
                    if ($writer instanceof Export_Parametrizable) {
                        $export->setWriterParams($writer->getParams());
                    }
                    $export->status = 'queued';
                    $export->save();
                    $session->unsetAll();

                    $jobDispatcher = Zend_Registry::get('job_dispatcher');
                    $jobDispatcher->setQueueName('export_exports');
                    try {
                        $jobDispatcher->sendLongRunning('Export_Job_Export', array(
                            'exportId' => $export->id,
                        ));
                        $this->flash('Export started');
                    } catch (Exception $e) {
                        $export->status = 'error';
                        $this->flash('Export start failed', 'error');
                    }

                    $this->redirect('export');
                }
            } else {
                $this->flash(__('Form is invalid'), 'error');
                foreach ($form->getMessages() as $messages) {
                    foreach ($messages as $message) {
                        $this->flash($message, 'error');
                    }
                }
            }
        }

        $form = call_user_func($formCallback);
        $this->view->form = $form;
    }

    protected function getStartFormsCallbacks($exporter)
    {
        $formsCallbacks = array();

        $writer = $exporter->getWriter();
        if ($writer instanceof Export_Parametrizable) {
            $formsCallbacks['writer'] = function() use($writer) {
                $writerForm = $writer->getParamsForm();
                $writerForm->addElement('hidden', 'current_form', array(
                    'value' => 'writer',
                ));
                $writerForm->addElement('submit', 'submit', array(
                    'label' => __('Continue'),
                ));
                $writerForm->addDisplayGroup(array('submit'), 'writer_submit');

                return $writerForm;
            };
        }

        $formsCallbacks['start'] = function() {
            $startForm = new Export_Form_ExporterStartForm();
            $startForm->addElement('hidden', 'current_form', array(
                'value' => 'start',
            ));

            return $startForm;
        };

        return $formsCallbacks;
    }

    protected function flash($message, $namespace = 'success')
    {
        $flashMessenger = $this->getHelper('FlashMessenger');
        $flashMessenger->addMessage($message, $namespace);
    }

    protected function validateAndSave(Zend_Form $form, Export_Exporter $exporter)
    {
        if ($form->isValid($_POST)) {
            $values = $form->getValues();
            $exporter->setPostData($values);
            try {
                $exporter->save();
                $this->flash(__('Exporter successfully saved'));
                $this->redirect('export');
            } catch (Exception $e) {
                $this->flash(sprintf('Save of exporter failed : %s', $e->getMessage()));
            }
        } else {
            $this->flash(__('Form is invalid'));
            foreach ($form->getErrorMessages() as $message) {
                $this->flash($message, 'error');
            }
        }
    }
}
