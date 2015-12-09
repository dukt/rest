<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m151209_000001_rest_remove_authentication_scopes_and_customScopes_columns extends BaseMigration
{
    /**
     * Any migration code in here is wrapped inside of a transaction.
     *
     * @return bool
     */
    public function safeUp()
    {
        // Drop `scopes` column

        Craft::log('Dropping the `scopes` column from the `rest_authentications` table...', LogLevel::Info, true);

        $this->dropColumn('rest_authentications', 'scopes');

        Craft::log('Done dropping the `scopes` column from the `rest_authentications` table.', LogLevel::Info, true);


        // Drop `customScopes` column

        Craft::log('Dropping the `customScopes` column from the `rest_authentications` table...', LogLevel::Info, true);

        $this->dropColumn('rest_authentications', 'customScopes');

        Craft::log('Done dropping the `customScopes` column from the `rest_authentications` table.', LogLevel::Info, true);

        return true;
    }
}
