# Email testing

This module contains commands to send an email based on the available templates.

## List templates

    bin/magento email:list

Returns the list of available emails.

## Send an email

    bin/magento email:send \
        --template customer_create_account_email_template \
        --store 0 \
        test@domain.com

Generate an email template and send it.

## Notes

* The email should be translated according to the given store language.

* If the frontend compilation is enabled for LESS, you won't be able to send emails.<br>
**Emogrifier** won't be able to parse the generated HTML if it's not properly compiled to CSS first.
