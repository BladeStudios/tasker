CREATE DATABASE IF NOT EXISTS tasker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE tasker.positions (
    id int(10) UNSIGNED DEFAULT NULL UNIQUE COMMENT 'position in the community; number refers to position name, ex. Admin, Operator, Moderator, User etc.',
    name varchar(32) NOT NULL COMMENT 'position name, like: Admin, Operator, Moderator, User etc.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tasker.online_statuses (
    id int(10) UNSIGNED DEFAULT NULL UNIQUE COMMENT 'online status ID',
    name varchar(32) NOT NULL COMMENT 'status name, like: online, offline, AFK etc.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tasker.timezones (
    id int(10) UNSIGNED DEFAULT NULL UNIQUE COMMENT 'timezone ID',
    name varchar(100) NOT NULL COMMENT 'timezone name, for example Europe/London',
    country_code varchar(2) NOT NULL COMMENT 'country code, for example PL for Europe/Warsaw',
    utc_offset_std int(11) DEFAULT 0 COMMENT 'offset in minutes from UTC standard time, like -120 for UTC-2:00',
    utc_offset_dst int(11) DEFAULT 0 COMMENT 'offset in minutes from UTC daylight saving time, like -120 for UTC-2:00',
    timezone_abbreviation_std varchar(5) NOT NULL COMMENT 'time zone abbreviation for standard time, like GMT',
    timezone_abbreviation_dst varchar(5) NOT NULL COMMENT 'time zone abbreviation for daylight saving time, like GMT'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tasker.users (
    id int(10) UNSIGNED NOT NULL UNIQUE COMMENT 'unique ID of each user entry',
    login varchar(32) DEFAULT NULL COMMENT 'login/username/nickname',
    password varchar(256) DEFAULT NULL COMMENT 'password hash',
    salt varchar(256) DEFAULT NULL COMMENT 'password salt',
    email varchar(64) DEFAULT NULL COMMENT 'e-mail',
    position int(10) UNSIGNED DEFAULT NULL COMMENT 'position in the community; number refers to position name, ex. Admin, Operator, Moderator, User etc.',
    status int(10) UNSIGNED DEFAULT NULL COMMENT 'online status; number refers to online status, ex. online, offline, AFK etc.',
    current_ip varchar(32) DEFAULT NULL COMMENT 'current IP of the user',
    last_ip varchar(32) DEFAULT NULL COMMENT 'previous IP of the user',
    last_activity DATETIME DEFAULT NULL COMMENT 'date and time when the user was last seen as online or active',
    browser varchar(32) DEFAULT NULL COMMENT 'name of the browser that user uses',
    system varchar(32) DEFAULT NULL COMMENT 'name of the Operating System that user uses',
    create_time DATETIME DEFAULT NULL COMMENT 'date and time when the user created his account',
    banned_until DATETIME DEFAULT NULL COMMENT 'date and time when the account will be unbanned',
    level int(10) UNSIGNED NOT NULL COMMENT 'current account level (depends on experience points)',
    experience BIGINT(20) UNSIGNED NOT NULL COMMENT 'current number of experience points for doing tasks',
    timezone_id int(10) UNSIGNED DEFAULT NULL COMMENT 'timezone ID',
    PRIMARY KEY (id),
    FOREIGN KEY (position) REFERENCES positions(id),
    FOREIGN KEY (status) REFERENCES online_statuses(id),
    FOREIGN KEY (timezone_id) REFERENCES timezones(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tasker.friendship_statuses (
    id int(10) UNSIGNED DEFAULT NULL UNIQUE COMMENT 'friendship status ID',
    name varchar(32) NOT NULL COMMENT 'status name, like: PENDING, ACCEPTED, REJECTED, BLOCKED etc.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tasker.friendships (
    id int(10) UNSIGNED DEFAULT NULL UNIQUE COMMENT 'friendship ID',
    inviter_id int(10) UNSIGNED NOT NULL COMMENT 'ID of user who invited the another user to the friendlist',
    invitee_id int(10) UNSIGNED NOT NULL COMMENT 'ID of user who was invited by another user to the friendlist',
    status_id int(10) UNSIGNED NOT NULL COMMENT 'friendship status ID',
    FOREIGN KEY (inviter_id) REFERENCES users(id),
    FOREIGN KEY (invitee_id) REFERENCES users(id),
    FOREIGN KEY (status_id) REFERENCES friendship_statuses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tasker.task_types (
    id int(10) UNSIGNED DEFAULT NULL UNIQUE COMMENT 'task type ID',
    name varchar(32) NOT NULL COMMENT 'task type name, like "washing the dishes"'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tasker.task_statuses (
    id int(10) UNSIGNED DEFAULT NULL UNIQUE COMMENT 'task status ID',
    name varchar(32) NOT NULL COMMENT 'task status name, like TODO, IN PROGRESS, PAUSED, DONE, REMOVED'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tasker.tasks (
    id int(10) UNSIGNED DEFAULT NULL UNIQUE COMMENT 'task ID',
    creator_id int(10) UNSIGNED NOT NULL COMMENT 'ID of user who created the task',
    executor_id int(10) UNSIGNED NOT NULL COMMENT 'ID of user who has to execute the task',
    type_id int(10) UNSIGNED NOT NULL COMMENT 'task type ID',
    name varchar(32) NOT NULL COMMENT 'task name, like "washing the dishes"',
    description TEXT DEFAULT NULL COMMENT 'task description',
    time_spent int(10) UNSIGNED NOT NULL COMMENT 'number of seconds spent doing the task (max is around 136.01 years)',
    started DATETIME DEFAULT NULL COMMENT 'date and time when the user started or restarted doing the task',
    stopped DATETIME DEFAULT NULL COMMENT 'date and time when the user stopped doing the task (by finishing it or pausing it)',
    status_id int(10) UNSIGNED NOT NULL COMMENT 'status type ID',
    FOREIGN KEY (creator_id) REFERENCES users(id),
    FOREIGN KEY (executor_id) REFERENCES users(id),
    FOREIGN KEY (type_id) REFERENCES task_types(id),
    FOREIGN KEY (status_id) REFERENCES task_statuses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;