 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @since         3.1.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Mailer;

use BadMethodCallException;
use Cake\Core\StaticConfigTrait;
use Cake\Event\EventListenerInterface;
use Cake\Log\Log;
use Cake\Mailer\Exception\MissingActionException;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\View\ViewBuilder;
use InvalidArgumentException;
use function Cake\Core\deprecationWarning;

/**
 * Mailer base class.
 *
 * Mailer classes let you encapsulate related Email logic into a reusable
 * and testable class.
 *
 * ## Defining Messages
 *
 * Mailers make it easy for you to define methods that handle email formatting
 * logic. For example:
 *
 * ```
 * class UserMailer extends Mailer
 * {
 *     public function resetPassword($user)
 *     {
 *         $this
 *             ->setSubject('Reset Password')
 *             ->setTo($user->email)
 *             ->set(['token' => $user->token]);
 *     }
 * }
 * ```
 *
 * Is a trivial example but shows how a mailer could be declared.
 *
 * ## Sending Messages
 *
 * After you have defined some messages you will want to send them:
 *
 * ```
 * $mailer = new UserMailer();
 * $mailer->send('resetPassword', $user);
 * ```
 *
 * ## Event Listener
 *
 * Mailers can also subscribe to application event allowing you to
 * decouple email delivery from your application code. By re-declaring the
 * `implementedEvents()` method you can define event handlers that can
 * convert events into email. For example, if your application had a user
 * registration event:
 *
 * ```
 * public function implementedEvents(): array
 * {
 *     return [
 *         'Model.afterSave' => 'onRegistration',
 *     ];
 * }
 *
 * public function onRegistration(EventInterface $event, EntityInterface $entity, ArrayObject $options)
 * {
 *     if ($entity->isNew()) {
 *          $this->send('welcome', [$entity]);
 *     }
 * }
 * ```
 *
 * The onRegistration method converts the application event into a mailer method.
 * Our mailer could either be registered in the application bootstrap, or
 * in the Table class' initialize() hook.
 *
 * @method $this setTo($email, $name = null) Sets "to" address. {@see \Cake\Mailer\Message::setTo()}
 * @method array getTo() Gets "to" address. {@see \Cake\Mailer\Message::getTo()}
 * @method $this setFrom($email, $name = null) Sets "from" address. {@see \Cake\Mailer\Message::setFrom()}
 * @method array getFrom() Gets "from" address. {@see \Cake\Mailer\Message::getFrom()}
 * @method $this setSender($email, $name = null) Sets "sender" address. {@see \Cake\Mailer\Message::setSender()}
 * @method array getSender() Gets "sender" address. {@see \Cake\Mailer\Message::getSender()}
 * @method $this setReplyTo($email, $name = null) Sets "Reply-To" address. {@see \Cake\Mailer\Message::setReplyTo()}
 * @method array getReplyTo() Gets "Reply-To" address. {@see \Cake\Mailer\Message::getReplyTo()}
 * @method $this addReplyTo($email, $name = null) Add "Reply-To" address. {@see \Cake\Mailer\Message::addReplyTo()}
 * @method $this setReadReceipt($email, $name = null) Sets Read Receipt (Disposition-Notification-To header).
 *   {@see \Cake\Mailer\Message::setReadReceipt()}
 * @method array getReadReceipt() Gets Read Receipt (Disposition-Notification-To header).
 *   {@see \Cake\Mailer\Message::getReadReceipt()}
 * @method $this setReturnPath($email, $name = null) Sets return path. {@see \Cake\Mailer\Message::setReturnPath()}
 * @method array getReturnPath() Gets return path. {@see \Cake\Mailer\Message::getReturnPath()}
 * @method $this addTo($email, $name = null) Add "To" address. {@see \Cake\Mailer\Message::addTo()}