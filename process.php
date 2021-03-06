<?php
$flds = array(

   //Email to send to 
  '$to:target'=>'jb@jasonbraun.me',

  //Default email message it comes from when an email is not submitted
  '$from:defaultfrom'=>'"jasonbraun.me" <jb@jasonbraun.me>',

  //Subject
  '$subject:subject'=>'Contact Submission',

  //Message in top of sent email
  '$top:readonly'=>'A submission has been made on your site, below are the provided details' . "\n\n",

  //Redirects to page once submitted - create a new page or change headers
  '$redirect:defaultredirect'=>'/thankyou.html',

  //Types available
  // from - This does not go into the email, but rather changes the email so the user to reply back to the given email address
  // single - single line
  // text - multi-line text, data is put onto the next line below the header
  // checkbox - Yes/No on checkbox (Checkbox should have a value greater than 0 assigned to it in HTML
  // antibot - (Honeypot) Checks if value is nothing, if not, the email does not send.
  'email:from'=>'',
  'name:single'=>'Name',
  'email:single'=>'Email Address',
  'comments:text'=>'Message',
  'normal:antibot'=>'normal',
       

  //List of all values passed from the form
  //THESE SHOULD MATCH KEYS FROM ABOVE
'system::checks'=>array('name','comments','email','normal'),

  //a post value to check if the form was actually posted
  'system::trigger'=>'email',
);

$pfw_redirect='/thank-you.html';

if(isset($_POST[$flds['system::trigger']])) {
  $ok=0;
  foreach($_POST as $k=>&$v) {$v = trim($v); $ok+= in_array($k,$flds['system::checks'])?1:0; } unset($v);
  $ok = ($ok==count($flds['system::checks'])?true:false);
  unset($flds['system::checks']);
  unset($flds['system::trigger']);
  if($ok) {
    $pfw_header = "From: jb@jasonbraun.me\n" . "Reply-To: jb@jasonbraun.me\n";
    $pfw_message = '';
    $pfw_subject = 'Form Submission';
    $pfw_email_to = "jb@jasonbraun.me";
    foreach ($flds as $key => $header) {
      $k = explode(':', $key);
      $v = @$_POST[$k[0]];
      unset($k[0]);
      $v = str_replace(array("\r","\n"),'',$v);
      foreach ($k as $cmd) {
        switch ($cmd) {
          case('defaultredirect'):
          {
            $pfw_redirect=$header;
            break;
          }
          case('redirect'):
          {
            $pfw_redirect=$v;
            break;
          }
          case('target'):
          {
            $pfw_email_to = $header;
            break;
          }
          case('subject'):
          {
            $pfw_subject = $header;
            break;
          }
          case('readonly'):
          {
            $pfw_message.=$header;
            break;
          }
          case('hidden'):
          {
            break;
          }
          case('defaultfrom'):
          {
            $pfw_header = "From: {$header}\n" . "Reply-To: {$header}\n";
            break;
          }
          case('antibot'):
          {
            if($v!='') {
              $ok=false;
            }
            break;
          }
          case('from'):
          {
            $pfw_header = "From: {$v}\n" . "Reply-To: {$v}\n";
            break;
          }
          case('single'):
          {
            $pfw_message .= $header . ': ' . $v . "\n";
            break;
          }
          case('checkbox'):
          {
            if((int)$v>0) {
              $v = 'Yes';
            } else {
              $v = 'No';
            }
            $pfw_message .= $header . ': ' . $v . "\n";
            break;
          }
          case('text'):
          {
            $pfw_message .= $header . ': ' . $v . "\n";
            break;
          }
        }
      }
    }
    if($ok) {
       mail($pfw_email_to, $pfw_subject ,$pfw_message ,$pfw_header ) ;
    }
  }
}

header( "Location: " . $pfw_redirect );
