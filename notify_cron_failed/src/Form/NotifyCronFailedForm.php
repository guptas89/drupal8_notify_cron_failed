<?php

/**
 * @file
 * Contains \Drupal\notify_cron_failed\NotifyCronFailedForm
 */

namespace Drupal\notify_cron_failed\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class NotifyCronFailedForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'notify_cron_failed_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return [
        'notify_cron_failed.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $site_config = $this->config('system.site');
    $site_mail = $site_config->get('mail');
    if (empty($site_mail)) {
      $site_mail = ini_get('sendmail_from');
    }
    $config = $this->configFactory->get('notify_cron_failed.settings');

    $form['notify_cron_failed'] = array(
        '#type' => 'fieldset',
        '#title' => t('Settings For Notify Cron Failed'),
        '#collapsible' => FALSE,
    );
    $form['notify_cron_failed']['notify_cron_failed_email_address'] = array(
        '#type' => 'email',
        '#title' => t('To'),
        '#default_value' => $config->get('email') ? $config->get('email') : $site_mail,
        '#size' => 60,
        '#maxlength' => 64,
        '#description' => t('Email address to which notification is to be sent'),
        '#required' => TRUE,
    );
    $form['notify_cron_failed']['notify_cron_failed_email_subject'] = array(
        '#type' => 'textfield',
        '#title' => t('Subject'),
        '#default_value' => $config->get('subject'),
        '#size' => 60,
        '#maxlength' => 64,
        '#description' => t('A brief summary of the topic of message'),
        '#required' => TRUE,
    );
    $form['notify_cron_failed']['notify_cron_failed_email_message'] = array(
        '#type' => 'textarea',
        '#title' => t('Message'),
        '#default_value' => $config->get('message'),
        '#description' => t('Message to be sent to user'),
        '#required' => TRUE,
    );
    $form['notify_cron_failed']['notify_cron_failed_when'] = array(
        '#type' => 'select',
        '#title' => t('When'),
        '#options' => array(
            1 => t('1'),
            2 => t('2'),
            3 => t('3'),
            4 => t('4'),
            5 => t('5'),
            6 => t('6'),
            7 => t('7'),
            8 => t('8'),
            9 => t('9'),
            10 => t('10'),
        ),
        '#default_value' => $config->get('when'),
        '#description' => t('Set this to <em>Number of days</em> after which email notification to be sent'),
        '#required' => TRUE,
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory()->getEditable('notify_cron_failed.settings')
            ->set('email', $form_state->getValue('notify_cron_failed_email_address'))
            ->set('subject', $form_state->getValue('notify_cron_failed_email_subject'))
            ->set('message', $form_state->getValue('notify_cron_failed_email_message'))
            ->set('when', $form_state->getValue('notify_cron_failed_when'))
            ->save();

    return parent::submitForm($form, $form_state);
  }

}
