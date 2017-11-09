<?php

/**
 * @file
 * Contains \Drupal\notify_cron_failed\EventSubscriber\NotifyCronFailedSubscriber
 */

namespace Drupal\notify_cron_failed\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NotifyCronFailedSubscriber implements EventSubscriberInterface {

  public function notifyOnCronFailed(GetResponseEvent $event) {
    $config = \Drupal::configFactory()->getEditable('notify_cron_failed.settings');
    $state = \Drupal::state();
    $last_cron_timestamp = $state->get('system.cron_last');
    $timestamp = REQUEST_TIME;
    $cron_email_alert_when = $config->get('when');
    $datediff = $timestamp - $last_cron_timestamp;
    $no_of_days = floor($datediff / (60 * 60 * 24));
    $cron_email_alert_status = $config->get('notify_cron_failed_status');
    if ($no_of_days > $cron_email_alert_when) {
      if ($cron_email_alert_status === 0) {
        $params = array(
            'subject' => $config->get('subject'),
            'body' => $config->get('message'),
        );
        $to = $config->get('message');
        $from = $config->get('message');
        \Drupal::service('plugin.manager.mail')->mail('notify_cron_failed', 'notify_cron_failed_key', $to, \Drupal::languageManager()->getDefaultLanguage()->getId(), $params);
        $config->set('notify_cron_failed_status', 1)->save();
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('notifyOnCronFailed');
    return $events;
  }

}
