<?php

class Painel_Form_Usuario extends Zend_Form
{
    public function init()
    {

        /**
         * Definições para o FORM
         */
        $this->setName( 'contato' );
        $this->setAction( '/contato' );
        $this->setMethod( 'post' );
        $this->setAttrib( 'enctype', 'multipart/form-data' );
        $this->setAttrib( 'id', 'form-contato' );
		
		// Add an email element
        $this->addElement('text', 'email', array(
            'label'      => 'Your email address:',
            'required'   => true,
            'class'   => "form-control",
            'filters'    => array('StringTrim'),
            'validators' => array(
                'EmailAddress',
            )
        ));
		
		// Add a captcha
        $this->addElement('captcha', 'captcha', array(
            'label'      => 'Please enter the 5 letters displayed below:',
            'required'   => true,
            'captcha'    => array(
                'captcha' => 'Figlet',
                'wordLen' => 5,
                'timeout' => 300
            )
        ));
		
		// Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => 'Gravar',
        ));
 
        // And finally add some CSRF protection
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
		
		
    }
}