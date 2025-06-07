<?php
namespace Config;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
  private function createMailer(): PHPMailer
  {

    $mailer = new PHPMailer(true);

    $mailer->isSMTP();
    $mailer->Host = $_ENV['SMTP_HOST'];
    $mailer->SMTPAuth = true;
    $mailer->Username = $_ENV['SMTP_USER'];
    $mailer->Password = $_ENV['SMTP_PASS'];
    $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mailer->Port = $_ENV['SMTP_PORT'];

    $mailer->isHTML(true);


    return $mailer;
  }

  public function sendQuoteNotification(array $quote): bool
  {
    $adminSent = $this->sendAdminNotification($quote);
    $userSent = $this->sendUserFollowUp($quote);

    return $adminSent && $userSent;
  }

  private function sendAdminNotification(array $quote): bool
  {
    try {
      $mailer = $this->createMailer();

      $mailer->setFrom($_ENV['SMTP_FROM'], $_ENV['APP_NAME']);
      $mailer->addAddress($_ENV['QUOTE_EMAIL']);
      $mailer->addReplyTo($quote['email'], $quote['fullnames']);
      $mailer->Subject = 'New Quote Request Submitted - Web Portal';

      $mailer->Body = <<<HTML
<html>
  <head>
    <meta charset="UTF-8" />
    <title>New Quotation request Submitted - Web Portal</title>
  </head>
  <body
    style="
      font-family: Arial, sans-serif;
      padding: 20px;
      background-color: #fff;
    "
  >
    <!-- Logo Section -->
    <div style="display: flex;align-items: center; justify-content: center; margin-bottom: 0.5rem;">
      <img
       src="{$_ENV['APP_URL']}/logo.png"
        alt="Felicity Solar Logo"
        style="max-width: 180px; height: auto"
      />
    </div>

    <div
      style="
        max-width: 600px;
        margin: auto;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      "
    >
      <!-- Header -->
      <div
        style="
          background-color: #ed7020;
          color: #ffffff;
          padding: 20px;
          text-align: center;
        "
      >
        <h2 style="margin: 0">New Quotation Submitted</h2>
      </div>

      <!-- Quote Details -->
      <div style="padding: 20px">
        <p style="margin-bottom: 10px">
          <strong>Full Name:</strong> {$quote['fullnames']}
        </p>
        <p style="margin-bottom: 10px">
          <strong>Email:</strong> {$quote['email']}
        </p>
        <p style="margin-bottom: 10px">
          <strong>Location:</strong> {$quote['location']}
        </p>
        <p style="margin-bottom: 10px">
          <strong>Budget Range:</strong> {$quote['budget_range']}
        </p>
        <p style="margin-bottom: 10px">
          <strong>Notes:</strong> {$quote['notes']}
        </p>
      </div>

      <!-- Footer -->
      <div
        style="
          background-color: #f1f1f1;
          text-align: center;
          padding: 15px;
          font-size: 12px;
          color: #555;
        "
      >
        <p style="margin: 0">
          This quote was submitted via the Felicity Solar website.
        </p>
      </div>
    </div>
  </body>
</html>
HTML;

      $mailer->send();
      return true;
    } catch (Exception $e) {
      error_log("Admin mail error: " . $e->getMessage());
      return false;
    }
  }
  public function sendOrderRequestNotification(array $data): bool
  {
    try {
      $mailer = $this->createMailer();

      $mailer->setFrom($_ENV['SMTP_FROM'], $_ENV['APP_NAME']);
      $mailer->addAddress($_ENV['QUOTE_EMAIL']);
      $mailer->addReplyTo($data['email'], $data['fullnames']);
      $mailer->Subject = 'New Order Submitted - Web Portal';

      $mailer->Body = <<<HTML
<html>
  <head>
    <meta charset="UTF-8" />
    <title>New Product Order request Submitted - Web Portal</title>
  </head>
  <body
    style="
      font-family: Arial, sans-serif;
      padding: 20px;
      background-color: #fff;
    "
  >
    <!-- Logo Section -->
    <div style="display: flex;align-items: center; justify-content: center; margin-bottom: 0.5rem;">
      <img
       src="{$_ENV['APP_URL']}/logo.png"
        alt="Felicity Solar Logo"
        style="max-width: 180px; height: auto"
      />
    </div>

    <div
      style="
        max-width: 600px;
        margin: auto;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      "
    >
      <!-- Header -->
      <div
        style="
          background-color: #ed7020;
          color: #ffffff;
          padding: 20px;
          text-align: center;
        "
      >
        <h2 style="margin: 0">New Request Submitted</h2>
      </div>

      <!-- Quote Details -->
      <div style="padding: 20px">
        <p style="margin-bottom: 10px">
          <strong>Full Name:</strong> {$data['fullnames']}
        </p>
        <p style="margin-bottom: 10px">
          <strong>Email:</strong> {$data['email']}
        </p>
        <p style="margin-bottom: 10px">
          <strong>Product:</strong> {$data['product_name']}
        </p>
        <p style="margin-bottom: 10px">
          <strong>Qty:</strong> {$data['qty']}
        </p>
        <p style="margin-bottom: 10px">
          <strong>Additional Message:</strong> {$data['additionalMessage']}
        </p>
      </div>

      <!-- Footer -->
      <div
        style="
          background-color: #f1f1f1;
          text-align: center;
          padding: 15px;
          font-size: 12px;
          color: #555;
        "
      >
        <p style="margin: 0">
          This request was submitted via the Felicity Solar website.
        </p>
      </div>
    </div>
  </body>
</html>
HTML;

      $mailer->send();
      return true;
    } catch (Exception $e) {
      error_log("Admin mail error: " . $e->getMessage());
      return false;
    }
  }
  public function sendQueryNotification(array $data): bool
  {
    try {
      $mailer = $this->createMailer();

      $mailer->setFrom($_ENV['SMTP_FROM'], $_ENV['APP_NAME']);
      $mailer->addAddress($_ENV['CONTACT_EMAIL']);
      $mailer->addReplyTo($data['email'], $data['firstname']);
      $mailer->Subject = 'New Query Submitted - Web Portal';

      $mailer->Body = <<<HTML
<html>
  <head>
    <meta charset="UTF-8" />
    <title>New Query Submitted - Web Portal</title>
  </head>
  <body
    style="
      font-family: Arial, sans-serif;
      padding: 20px;
      background-color: #fff;
    "
  >
    <!-- Logo Section -->
    <div style="display: flex;align-items: center; justify-content: center; margin-bottom: 0.5rem;">
      <img
       src="{$_ENV['APP_URL']}/logo.png"
        alt="Felicity Solar Logo"
        style="max-width: 180px; height: auto"
      />
    </div>

    <div
      style="
        max-width: 600px;
        margin: auto;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      "
    >
      <!-- Header -->
      <div
        style="
          background-color: #ed7020;
          color: #ffffff;
          padding: 20px;
          text-align: center;
        "
      >
        <h2 style="margin: 0">New Query Submitted</h2>
      </div>

      <!-- Quote Details -->
      <div style="padding: 20px">
        <p style="margin-bottom: 10px">
          <strong>Full Name:</strong> {$data['firstname']} {$data['lastname']}
        </p>
        <p style="margin-bottom: 10px">
          <strong>Email:</strong> {$data['email']}
        </p>
        <p style="margin-bottom: 10px">
          <strong>Phone:</strong> {$data['phone']}
        </p>
        <p style="margin-bottom: 10px">
          <strong>Message:</strong> {$data['message']}
        </p>
      </div>

      <!-- Footer -->
      <div
        style="
          background-color: #f1f1f1;
          text-align: center;
          padding: 15px;
          font-size: 12px;
          color: #555;
        "
      >
        <p style="margin: 0">
          This request was submitted via the Felicity Solar website.
        </p>
      </div>
    </div>
  </body>
</html>
HTML;

      $mailer->send();
      return true;
    } catch (Exception $e) {
      error_log("Admin mail error: " . $e->getMessage());
      return false;
    }
  }

  private function sendUserFollowUp(array $quote): bool
  {
    try {
      $mailer = $this->createMailer();

      $mailer->setFrom($_ENV['SMTP_FROM'], $_ENV['APP_NAME']);
      $mailer->addAddress($quote['email'], $quote['fullnames']);
      $mailer->Subject = 'We Received Your Quotation Request';
      $mailer->Body = <<<HTML
<html>
  <head><meta charset="UTF-8" /></head>
  <body style="font-family: Arial, sans-serif; padding: 20px;">
    <div style="text-align: center; margin-bottom: 30px;">
      <img  src="{$_ENV['APP_URL']}/logo.png" alt="Felicity Solar Logo" style="max-width: 180px;" />
    </div>
    <h3>Hi {$quote['fullnames']},</h3>
    <p>Thank you for submitting your quotation request to <strong>{$_ENV['APP_NAME']}</strong>.</p>
    <p>We’ve received your request and our team will get in touch with you shortly.</p>
    <p>If you have any urgent questions, feel free to reply to this email.</p>
    <br />
    <p>Warm regards,<br />The Felicity Solar Team</p>
  </body>
</html>
HTML;

      $mailer->send();
      return true;
    } catch (Exception $e) {
      error_log("Follow-up mail error: " . $e->getMessage());
      return false;
    }
  }
}
