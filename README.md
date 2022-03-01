# Magento 2 - Email testing

Console commands to generate an email based on the available templates.

## Install

    composer require ubermanu/magento2-email

## Usage

List the available email templates:

    php bin/magento email:list

Generate an email and send it:

    php bin/magento email:send \
        --template customer_create_account_email_template \
        --store 0 \
        test@domain.com

Generate an email and dump its content:

    php bin/magento email:dump \
        --template customer_create_account_email_template \
        --store 0

## Variables

It is possible to inject variables in the generated email using a YAML file.

For example:

```yaml
customer:
  id: 1
  name: John Doe
  email: john@example.com
```

And use this data in the email:

    php bin/magento email:send \
        --template customer_create_account_email_template \
        --vars variables.yaml \
        test@domain.com

## Notes

The email should be translated according to the given store language.

It's not possible to send an email with frontend compilation enabled.<br>
[Emogrifier](https://github.com/MyIntervals/emogrifier) won't be able to parse the generated HTML if it's not properly compiled to CSS first.
