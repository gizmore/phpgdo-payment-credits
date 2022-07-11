<?php
namespace GDO\PaymentCredits\Method;

use GDO\Form\GDT_Form;
use GDO\Form\MethodForm;
use GDO\User\GDT_User;
use GDO\PaymentCredits\GDT_Credits;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Submit;
use GDO\User\GDO_User;
use GDO\Mail\Mail;

final class GrantCredits extends MethodForm
{
	public function getPermission() : ?string { return 'staff'; }
	
	public function createForm(GDT_Form $form) : void
	{
		$form->addFields(array(
			GDT_User::make('user')->withCompletion()->notNull(),
			GDT_Credits::make('credits')->notNull(),
			GDT_AntiCSRF::make(),
		));
		$form->actions()->addField(GDT_Submit::make());
	}
	
	/**
	 * @return GDO_User
	 */
	public function getUser()
	{
		return $this->getForm()->getFormValue('user');
	}
	
	public function formValidated(GDT_Form $form)
	{
		$user = $this->getUser();
		$credits = $form->getFormVar('credits');
// 		$creditsBefore = $user->getCredits();
		$user->increase('user_credits', $credits);
		$creditsAfter = $user->getCredits();
		
		$this->sendMail($user, $credits, $creditsAfter);
		
		return $this->message('msg_credits_granted', [$credits, $user->renderUserName(), $creditsAfter]);
	}
	
	private function sendMail(GDO_User $user, $credits, $creditsAfter)
	{
		$mail = Mail::botMail();
		$mail->setSubject(tusr($user, 'mail_subj_credits_granted'));
		$tVars = array(
			$user->renderUserName(),
			$credits,
			sitename(),
			$creditsAfter,
		);
		$mail->setBody(tusr($user, 'mail_body_credits_granted', $tVars));
		$mail->sendToUser($user);
	}
	
}
