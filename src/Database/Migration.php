<?php

namespace Telegram\Database;

use Telegram\Bot;

class Migration
{
    public static function up()
    {
        $schema = Bot::getInstance()->db()->schema();

        if (!$schema->hasTable('users')) {
            $schema->create('users', function ($table) {
                $table->id();
                $table->bigInteger('user_id')->unique();
                $table->boolean('active');
                $table->text('fullname')->nullable();
                $table->text('firstname')->nullable();
                $table->string('lastname')->nullable();
                $table->longText('username')->nullable();
                $table->string('lang', 3);
                $table->string('role')->nullable();
                $table->string('nickname')->nullable();
                $table->string('emoji')->nullable();
                $table->text('photo')->nullable();
                $table->boolean('banned');
                $table->text('ban_comment')->nullable();
                $table->bigInteger('ban_date_from')->nullable();
                $table->bigInteger('ban_date_to')->nullable();
                $table->text('state_name')->nullable();
                $table->longText('state_data')->nullable();
                $table->string('source')->nullable();
                $table->string('version');
                $table->bigInteger('first_message');
                $table->bigInteger('last_message');
                $table->longText('note')->nullable();
            });
        }
        
        if (!$schema->hasTable('stats_new_users')) {
            $schema->create('stats_new_users', function ($table) {
                $table->id();
                $table->bigInteger('date')->nullable();
                $table->integer('count')->nullable();
            });
        }

        
        if (!$schema->hasTable('stats_messages')) {
            $schema->create('stats_messages', function ($table) {
                $table->id();
                $table->bigInteger('date')->nullable();
                $table->integer('count')->nullable();
            });
        }
        
        if (!$schema->hasTable('messages')) {
            $schema->create('messages', function ($table) {
                $table->id();
                $table->bigInteger('date')->nullable();
                $table->bigInteger('user_id')->nullable();
                $table->text('user')->nullable();
                $table->mediumText('value')->nullable();
            });
        }
    }

    public static function down()
    {
        $schema = Bot::getInstance()->db()->schema();
        
        $schema->dropIfExists('users');
        $schema->dropIfExists('stats_new_users');
        $schema->dropIfExists('stats_messages');
        $schema->dropIfExists('messages');
    }
}
