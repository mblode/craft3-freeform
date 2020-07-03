<?php

namespace Solspace\Freeform\migrations;

use Solspace\Commons\Migrations\ForeignKey;
use Solspace\Commons\Migrations\StreamlinedInstallMigration;
use Solspace\Commons\Migrations\Table;

/**
 * Install migration.
 */
class Install extends StreamlinedInstallMigration
{
    /**
     * @return array
     */
    protected function defineTableData(): array
    {
        return [
            (new Table('freeform_forms'))
                ->addField('id', $this->primaryKey())
                ->addField('name', $this->string(100)->notNull())
                ->addField('handle', $this->string(100)->notNull()->unique())
                ->addField('spamBlockCount', $this->integer()->unsigned()->notNull()->defaultValue(0))
                ->addField('submissionTitleFormat', $this->string(255)->notNull())
                ->addField('description', $this->text())
                ->addField('layoutJson', $this->mediumText())
                ->addField('returnUrl', $this->string(255))
                ->addField('defaultStatus', $this->integer()->unsigned())
                ->addField('formTemplateId', $this->integer()->unsigned())
                ->addField('color', $this->string(10))
                ->addField('optInDataStorageTargetHash', $this->string(20)->null())
                ->addField('limitFormSubmissions', $this->string(20)->null())
                ->addField('extraPostUrl', $this->string(255)->null())
                ->addField('extraPostTriggerPhrase', $this->string(255)->null()),

            (new Table('freeform_fields'))
                ->addField('id', $this->primaryKey())
                ->addField('type', $this->string(50)->notNull())
                ->addField('handle', $this->string(255)->notNull()->unique())
                ->addField('label', $this->string(255))
                ->addField('required', $this->boolean()->defaultValue(false))
                ->addField('instructions', $this->text())
                ->addField('metaProperties', $this->text())
                ->addIndex(['type']),

            (new Table('freeform_notifications'))
                ->addField('id', $this->primaryKey())
                ->addField('name', $this->string(255)->notNull())
                ->addField('handle', $this->string(255)->notNull()->unique())
                ->addField('subject', $this->string(255)->notNull())
                ->addField('description', $this->text())
                ->addField('fromName', $this->string(255)->notNull())
                ->addField('fromEmail', $this->string(255)->notNull())
                ->addField('replyToEmail', $this->string(255))
                ->addField('cc', $this->string(255))
                ->addField('bcc', $this->string(255))
                ->addField('bodyHtml', $this->text())
                ->addField('bodyText', $this->text())
                ->addField('includeAttachments', $this->boolean()->defaultValue(true))
                ->addField('presetAssets', $this->string(255))
                ->addField('sortOrder', $this->integer()),

            (new Table('freeform_integrations'))
                ->addField('id', $this->primaryKey())
                ->addField('name', $this->string(255)->notNull())
                ->addField('handle', $this->string(255)->notNull()->unique())
                ->addField('type', $this->string(50)->notNull())
                ->addField('class', $this->string(255))
                ->addField('accessToken', $this->string(255))
                ->addField('settings', $this->text())
                ->addField('forceUpdate', $this->boolean()->defaultValue(false))
                ->addField('lastUpdate', $this->dateTime())
                ->addIndex(['type']),

            (new Table('freeform_mailing_lists'))
                ->addField('id', $this->primaryKey())
                ->addField('integrationId', $this->integer()->notNull())
                ->addField('resourceId', $this->string(255)->notNull())
                ->addField('name', $this->string(255)->notNull())
                ->addField('memberCount', $this->integer())
                ->addIndex(['integrationId', 'resourceId'], true)
                ->addForeignKey('integrationId', 'freeform_integrations', 'id', ForeignKey::CASCADE),

            (new Table('freeform_mailing_list_fields'))
                ->addField('id', $this->primaryKey())
                ->addField('mailingListId', $this->integer()->notNull())
                ->addField('label', $this->string(255)->notNull())
                ->addField('handle', $this->string(255)->notNull())
                ->addField('type', $this->string(50)->notNull())
                ->addField('required', $this->boolean()->defaultValue(false))
                ->addIndex(['type'])
                ->addForeignKey('mailingListId', 'freeform_mailing_lists', 'id', ForeignKey::CASCADE),

            (new Table('freeform_crm_fields'))
                ->addField('id', $this->primaryKey())
                ->addField('integrationId', $this->integer()->notNull())
                ->addField('label', $this->string(255)->notNull())
                ->addField('handle', $this->string(255)->notNull())
                ->addField('type', $this->string(50)->notNull())
                ->addField('required', $this->boolean()->defaultValue(false))
                ->addIndex(['type'])
                ->addForeignKey('integrationId', 'freeform_integrations', 'id', ForeignKey::CASCADE),

            (new Table('freeform_payment_gateway_fields'))
                ->addField('id', $this->primaryKey())
                ->addField('integrationId', $this->integer()->notNull())
                ->addField('label', $this->string(255)->notNull())
                ->addField('handle', $this->string(255)->notNull())
                ->addField('type', $this->string(50)->notNull())
                ->addField('required', $this->boolean()->defaultValue(false))
                ->addIndex(['type'])
                ->addForeignKey('integrationId', 'freeform_integrations', 'id', ForeignKey::CASCADE),

            (new Table('freeform_statuses'))
                ->addField('id', $this->primaryKey())
                ->addField('name', $this->string(255)->notNull())
                ->addField('handle', $this->string(255)->notNull()->unique())
                ->addField('color', $this->string(30))
                ->addField('isDefault', $this->boolean())
                ->addField('sortOrder', $this->integer()),

            (new Table('freeform_unfinalized_files'))
                ->addField('id', $this->primaryKey())
                ->addField('assetId', $this->integer()->notNull()),

            (new Table('freeform_submissions'))
                ->addField('id', $this->primaryKey())
                ->addField('incrementalId', $this->integer()->notNull())
                ->addField('statusId', $this->integer())
                ->addField('formId', $this->integer()->notNull())
                ->addField('token', $this->string(100)->notNull())
                ->addField('ip', $this->string(46)->null())
                ->addField('isSpam', $this->boolean()->defaultValue(false))
                ->addIndex(['incrementalId'], true)
                ->addIndex(['token'], true)
                ->addForeignKey('id', 'elements', 'id', ForeignKey::CASCADE)
                ->addForeignKey('formId', 'freeform_forms', 'id', ForeignKey::CASCADE)
                ->addForeignKey('statusId', 'freeform_statuses', 'id', ForeignKey::CASCADE),

            (new Table('freeform_integrations_queue'))
                ->addField('id', $this->primaryKey())
                ->addField('submissionId', $this->integer()->notNull())
                ->addField('integrationType', $this->string(50)->notNull())
                ->addField('status', $this->string(50)->notNull())
                ->addField('fieldHash', $this->string(20))
                ->addIndex(['status'], false)
                ->addForeignKey('submissionId', 'freeform_submissions', 'id', ForeignKey::CASCADE)
                ->addForeignKey('id', 'freeform_mailing_list_fields', 'id', ForeignKey::CASCADE),

            // Pro
            (new Table('freeform_export_profiles'))
                ->addField('id', $this->primaryKey())
                ->addField('formId', $this->integer()->notNull())
                ->addField('name', $this->string(255)->notNull()->unique())
                ->addField('limit', $this->integer())
                ->addField('dateRange', $this->string(255))
                ->addField('fields', $this->text()->notNull())
                ->addField('filters', $this->text())
                ->addField('statuses', $this->text()->notNull())
                ->addForeignKey('formId', 'freeform_forms', 'id', ForeignKey::CASCADE),

            (new Table('freeform_export_settings'))
                ->addField('id', $this->primaryKey())
                ->addField('userId', $this->integer()->notNull())
                ->addField('setting', $this->mediumText())
                ->addForeignKey('userId', 'users', 'id', ForeignKey::CASCADE),

            // Payments
            (new Table('freeform_payments_subscription_plans'))
                ->addField('id', $this->primaryKey())
                ->addField('integrationId', $this->integer()->notNull())
                ->addField('resourceId', $this->string(255))
                ->addField('name', $this->string(255))
                ->addField('status', $this->string(20))
                ->addForeignKey('integrationId', 'freeform_integrations', 'id', ForeignKey::CASCADE),

            (new Table('freeform_payments_payments'))
                ->addField('id', $this->primaryKey())
                ->addField('integrationId', $this->integer()->notNull())
                ->addField('submissionId', $this->integer()->notNull())
                ->addField('subscriptionId', $this->integer())
                ->addField('resourceId', $this->string(50))
                ->addField('amount', $this->float(2))
                ->addField('currency', $this->string(3))
                ->addField('last4', $this->smallInteger())
                ->addField('status', $this->string(20))
                ->addField('metadata', $this->mediumText())
                ->addField('errorCode', $this->string(20))
                ->addField('errorMessage', $this->string(255))
                ->addForeignKey('submissionId', 'freeform_submissions', 'id', ForeignKey::CASCADE)
                ->addForeignKey('subscriptionId', 'freeform_payments_subscriptions', 'id', ForeignKey::CASCADE)
                ->addForeignKey('integrationId', 'freeform_integrations', 'id', ForeignKey::CASCADE)
                ->addIndex(['integrationId', 'resourceId'], true),

            (new Table('freeform_payments_subscriptions'))
                ->addField('id', $this->primaryKey())
                ->addField('integrationId', $this->integer()->notNull())
                ->addField('submissionId', $this->integer()->notNull())
                ->addField('planId', $this->integer()->notNull())
                ->addField('resourceId', $this->string(50))
                ->addField('amount', $this->float(2))
                ->addField('currency', $this->string(3))
                ->addField('interval', $this->string(20))
                ->addField('intervalCount', $this->smallInteger()->null())
                ->addField('last4', $this->smallInteger())
                ->addField('status', $this->string(20))
                ->addField('metadata', $this->mediumText())
                ->addField('errorCode', $this->string(20))
                ->addField('errorMessage', $this->string(255))
                ->addForeignKey('submissionId', 'freeform_submissions', 'id', ForeignKey::CASCADE)
                ->addForeignKey('integrationId', 'freeform_integrations', 'id', ForeignKey::CASCADE)
                ->addForeignKey('planId', 'freeform_payments_subscription_plans', 'id', ForeignKey::CASCADE)
                ->addIndex(['integrationId', 'resourceId'], true),

            (new Table('freeform_webhooks'))
                ->addField('id', $this->primaryKey())
                ->addField('type', $this->string()->notNull())
                ->addField('name', $this->string()->notNull())
                ->addField('webhook', $this->string()->notNull())
                ->addField('settings', $this->text())
                ->addIndex(['type']),

            (new Table('freeform_webhooks_form_relations'))
                ->addField('id', $this->primaryKey())
                ->addField('webhookId', $this->integer()->notNull())
                ->addField('formId', $this->integer()->notNull())
                ->addIndex(['webhookId'])
                ->addIndex(['formId'])
                ->addForeignKey('webhookId', 'freeform_webhooks', 'id', ForeignKey::CASCADE)
                ->addForeignKey('formId', 'freeform_forms', 'id', ForeignKey::CASCADE),

            (new Table('freeform_submission_notes'))
                ->addField('id', $this->primaryKey())
                ->addField('submissionId', $this->integer()->notNull())
                ->addField('note', $this->text())
                ->addForeignKey('submissionId', 'freeform_submissions', 'id', ForeignKey::CASCADE),

            (new Table('freeform_spam_reason'))
                ->addField('id', $this->primaryKey())
                ->addField('submissionId', $this->integer()->notNull())
                ->addField('reasonType', $this->string(100)->notNull())
                ->addField('reasonMessage', $this->text())
                ->addIndex(['submissionId', 'reasonType'])
                ->addForeignKey('submissionId', 'freeform_submissions', 'id', ForeignKey::CASCADE),
        ];
    }
}
