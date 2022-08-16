CREATE DATABASE IF NOT EXISTS tasker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `tasker`.`positions` (
    `id` int(11) DEFAULT NULL UNIQUE COMMENT 'position in the community; number refers to position name, ex. Admin, Operator, Moderator, User etc.',
    `name` varchar(32) NOT NULL COMMENT 'position name, like: Admin, Operator, Moderator, User etc.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tasker`.`online_statuses` (
    `id` int(11) DEFAULT NULL UNIQUE COMMENT 'online status ID',
    `name` varchar(32) NOT NULL COMMENT 'status name, like: online, offline, AFK etc.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tasker`.`users` (
  `id` int(11) NOT NULL COMMENT 'unique ID of each user entry',
  `login` varchar(32) DEFAULT NULL COMMENT 'login/username/nickname',
  `password` varchar(256) DEFAULT NULL COMMENT 'password hash',
  `salt` varchar(256) DEFAULT NULL COMMENT 'password salt',
  `email` varchar(64) DEFAULT NULL COMMENT 'e-mail',
  `position` int(11) DEFAULT NULL COMMENT 'position in the community; number refers to position name, ex. Admin, Operator, Moderator, User etc.',
  `status` int(11) DEFAULT NULL COMMENT 'online status; number refers to online status, ex. online, offline, AFK etc.',
  `current_ip` varchar(32) DEFAULT NULL COMMENT 'current IP of the user',
  `last_ip` varchar(32) DEFAULT NULL COMMENT 'previous IP of the user',
  `last_activity` DATETIME DEFAULT NULL COMMENT 'date and time when the user was last seen as online or active',
  `browser` varchar(32) DEFAULT NULL COMMENT 'name of the browser that user uses',
  `system` varchar(32) DEFAULT NULL COMMENT 'name of the Operating System that user uses',
  `create_time` DATETIME DEFAULT NULL COMMENT 'date and time when the user created his account',
  `banned_until` DATETIME DEFAULT NULL COMMENT 'date and time when the account will be unbanned',
  `level` int(11) NOT NULL COMMENT 'current account level (depends on experience points)',
  `experience` int(11) NOT NULL COMMENT 'current number of experience points for doing tasks'
    PRIMARY KEY (id),
    FOREIGN KEY (`position`) REFERENCES positions(id),
    FOREIGN KEY (`status`) REFERENCES online_statuses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tasker`.`friendship_statuses` (
    `id` int(11) DEFAULT NULL UNIQUE COMMENT 'friendship status ID',
    `name` varchar(32) NOT NULL COMMENT 'status name, like: PENDING, ACCEPTED, REJECTED, BLOCKED etc.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tasker`.`friendships` (
    `id` int(11) DEFAULT NULL UNIQUE COMMENT 'friendship ID',
    `inviter_id` int(11) NOT NULL COMMENT 'ID of user who invited the another user to the friendlist',
    `invitee_id` int(11) NOT NULL COMMENT 'ID of user who was invited by another user to the friendlist',
    `status_id` int(11) NOT NULL COMMENT 'friendship status ID',
    FOREIGN KEY ('inviter_id') REFERENCES users(id),
    FOREIGN KEY ('invitee_id') REFERENCES users(id),
    FOREIGN KEY ('status_id') REFERENCES friendship_statuses(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;