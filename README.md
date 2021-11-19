# Email testing

This module contains commands to send an email based on the available templates.

## Install

    composer require ubermanu/magento2-email

## Usage

Returns a list of available email templates:

    bin/magento email:list

Generate an email template and send it:

    bin/magento email:send \
        --template customer_create_account_email_template \
        --store 0 \
        test@domain.com

## Notes

The email should be translated according to the given store language.

It's not possible to send an email with frontend compilation enabled.<br>
`Emogrifier` won't be able to parse the generated HTML if it's not properly compiled to CSS first.
