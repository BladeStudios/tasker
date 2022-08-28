CREATE DATABASE IF NOT EXISTS tasker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tasker.users (
    id int(10) UNSIGNED AUTO_INCREMENT COMMENT 'unique ID of each user entry',
    login varchar(32) DEFAULT NULL COMMENT 'login/username/nickname',
    password varchar(256) DEFAULT NULL COMMENT 'password hash',
    salt varchar(256) DEFAULT NULL COMMENT 'password salt',
    email varchar(64) DEFAULT NULL COMMENT 'e-mail',
    position_id int(10) UNSIGNED DEFAULT NULL COMMENT 'position in the community; number refers to position name, ex. Admin, Operator, Moderator, User etc.',
    status_id int(10) UNSIGNED DEFAULT NULL COMMENT 'online status; number refers to online status, ex. online, offline, AFK etc.',
    last_ip varchar(32) DEFAULT NULL COMMENT 'previous IP of the user',
    current_ip varchar(32) DEFAULT NULL COMMENT 'current IP of the user',
    online_from DATETIME DEFAULT NULL COMMENT 'date and time when the user has logged in',
    total_online_time BIGINT(20) UNSIGNED NOT NULL COMMENT 'total number of seconds user has been online from its first login',
    last_activity DATETIME DEFAULT NULL COMMENT 'date and time when the user was last seen as online or active',
    browser varchar(32) DEFAULT NULL COMMENT 'name of the browser that user uses',
    system varchar(32) DEFAULT NULL COMMENT 'name of the Operating System that user uses',
    create_time DATETIME DEFAULT NULL COMMENT 'date and time when the user created his account',
    banned_until DATETIME DEFAULT NULL COMMENT 'date and time when the account will be unbanned',
    level int(10) UNSIGNED NOT NULL COMMENT 'current account level (depends on experience points)',
    experience BIGINT(20) UNSIGNED NOT NULL COMMENT 'current number of experience points for doing tasks',
    time_spent_tasks_overall BIGINT(20) UNSIGNED NOT NULL COMMENT 'total number of seconds user has been doing any tasks',
    timezone varchar(100) DEFAULT NULL COMMENT 'timezone name',
    PRIMARY KEY (id)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE tasker.friendships (
    id int(10) UNSIGNED AUTO_INCREMENT COMMENT 'friendship ID',
    inviter_id int(10) UNSIGNED NOT NULL COMMENT 'ID of user who invited the another user to the friendlist',
    invitee_id int(10) UNSIGNED NOT NULL COMMENT 'ID of user who was invited by another user to the friendlist',
    status_id int(10) UNSIGNED NOT NULL COMMENT 'friendship status ID',
    PRIMARY KEY (id),
    FOREIGN KEY (inviter_id) REFERENCES users(id),
    FOREIGN KEY (invitee_id) REFERENCES users(id)
) DEFAULT CHARSET=utf8mb4;

CREATE TABLE tasker.tasks (
    id int(10) UNSIGNED AUTO_INCREMENT COMMENT 'task ID',
    name varchar(60) NOT NULL COMMENT 'task name, like "washing the dishes"',
    description TEXT DEFAULT NULL COMMENT 'task description',
    time_spent int(10) UNSIGNED NOT NULL COMMENT 'number of seconds spent doing the task (max is around 136.01 years)',
    created DATETIME DEFAULT NULL COMMENT 'date and time when the task was created',
    started DATETIME DEFAULT NULL COMMENT 'date and time when the user started or restarted doing the task',
    stopped DATETIME DEFAULT NULL COMMENT 'date and time when the user stopped doing the task (by finishing it or pausing it)',
    deadline DATETIME DEFAULT NULL COMMENT 'date when the task should be finished',
    difficulty_id int(10) UNSIGNED NOT NULL COMMENT 'difficulty level ID',
    status_id int(10) UNSIGNED NOT NULL COMMENT 'status type ID',
    total_exp int(10) UNSIGNED NOT NULL COMMENT 'total experience gained for the task',
    creator_id int(10) UNSIGNED NOT NULL COMMENT 'ID of user who created the task',
    executor_id int(10) UNSIGNED NOT NULL COMMENT 'ID of user who has to execute the task',
    type_id int(10) UNSIGNED NOT NULL COMMENT 'task type ID',
    visibility_id int(10) UNSIGNED NOT NULL COMMENT 'task visibility level for other users',
    PRIMARY KEY (id),
    FOREIGN KEY (creator_id) REFERENCES users(id),
    FOREIGN KEY (executor_id) REFERENCES users(id)
) DEFAULT CHARSET=utf8mb4;