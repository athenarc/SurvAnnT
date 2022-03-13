-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Φιλοξενητής: 127.0.0.1
-- Χρόνος δημιουργίας: 13 Μαρ 2022 στις 17:27:52
-- Έκδοση διακομιστή: 10.4.11-MariaDB
-- Έκδοση PHP: 7.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Βάση δεδομένων: `survannt`
--

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `auth_assignment`
--

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `auth_item`
--

CREATE TABLE `auth_item` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` text DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `group_code` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Άδειασμα δεδομένων του πίνακα `auth_item`
--

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`, `group_code`) VALUES
('/*', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/debug/*', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/debug/default/*', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/debug/default/db-explain', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/debug/default/download-mail', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/debug/default/index', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/debug/default/toolbar', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/debug/default/view', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/debug/user/*', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/debug/user/reset-identity', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/debug/user/set-identity', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/gii/*', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/gii/default/*', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/gii/default/action', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/gii/default/diff', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/gii/default/index', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/gii/default/preview', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/gii/default/view', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/site/*', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/site/about', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/site/badges-create', 3, NULL, NULL, NULL, 1645087080, 1645087080, NULL),
('/site/captcha', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/site/contact', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/site/error', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/site/index', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/site/invite-user', 3, NULL, NULL, NULL, 1645966191, 1645966191, NULL),
('/site/leaderboard', 3, NULL, NULL, NULL, 1646663654, 1646663654, NULL),
('/site/login', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/site/logout', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/site/my-surveys-view', 3, NULL, NULL, NULL, 1643823224, 1643823224, NULL),
('/site/participants-invite', 3, NULL, NULL, NULL, 1644865983, 1644865983, NULL),
('/site/questions-create', 3, NULL, NULL, NULL, 1644865983, 1644865983, NULL),
('/site/request-participation', 3, NULL, NULL, NULL, 1645956310, 1645956310, NULL),
('/site/resource-create', 3, NULL, NULL, NULL, 1645087080, 1645087080, NULL),
('/site/survey-create', 3, NULL, NULL, NULL, 1643818553, 1643818553, NULL),
('/site/survey-create-new', 3, NULL, NULL, NULL, 1646753414, 1646753414, NULL),
('/site/survey-delete', 3, NULL, NULL, NULL, 1643818553, 1643818553, NULL),
('/site/survey-overview', 3, NULL, NULL, NULL, 1646566575, 1646566575, NULL),
('/site/survey-participants', 3, NULL, NULL, NULL, 1643898999, 1643898999, NULL),
('/site/survey-rate', 3, NULL, NULL, NULL, 1643823224, 1643823224, NULL),
('/site/surveys-statistics', 3, NULL, NULL, NULL, 1647163311, 1647163311, NULL),
('/site/surveys-view', 3, NULL, NULL, NULL, 1643818553, 1643818553, NULL),
('/site/user-requests', 3, NULL, NULL, NULL, 1645966191, 1645966191, NULL),
('/user-management/*', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/user-management/auth-item-group/*', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth-item-group/bulk-activate', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth-item-group/bulk-deactivate', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth-item-group/bulk-delete', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth-item-group/create', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth-item-group/delete', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth-item-group/grid-page-size', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth-item-group/grid-sort', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth-item-group/index', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth-item-group/toggle-attribute', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth-item-group/update', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth-item-group/view', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth/*', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth/captcha', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth/change-own-password', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/user-management/auth/confirm-email', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth/confirm-email-receive', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth/confirm-registration-email', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth/login', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth/logout', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth/password-recovery', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth/password-recovery-receive', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/auth/registration', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/permission/*', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/permission/bulk-activate', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/permission/bulk-deactivate', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/permission/bulk-delete', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/permission/create', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/permission/delete', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/permission/grid-page-size', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/permission/grid-sort', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/permission/index', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/permission/refresh-routes', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/permission/set-child-permissions', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/permission/set-child-routes', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/permission/toggle-attribute', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/permission/update', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/permission/view', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/role/*', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/role/bulk-activate', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/role/bulk-deactivate', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/role/bulk-delete', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/role/create', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/role/delete', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/role/grid-page-size', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/role/grid-sort', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/role/index', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/role/set-child-permissions', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/role/set-child-roles', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/role/toggle-attribute', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/role/update', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/role/view', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user-permission/*', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user-permission/set', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/user-management/user-permission/set-roles', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/user-management/user-visit-log/*', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user-visit-log/bulk-activate', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user-visit-log/bulk-deactivate', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user-visit-log/bulk-delete', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user-visit-log/create', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user-visit-log/delete', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user-visit-log/grid-page-size', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user-visit-log/grid-sort', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user-visit-log/index', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user-visit-log/toggle-attribute', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user-visit-log/update', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user-visit-log/view', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user/*', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user/bulk-activate', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/user-management/user/bulk-deactivate', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/user-management/user/bulk-delete', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/user-management/user/change-password', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/user-management/user/create', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/user-management/user/delete', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/user-management/user/grid-page-size', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/user-management/user/grid-sort', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user/index', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/user-management/user/toggle-attribute', 3, NULL, NULL, NULL, 1643795446, 1643795446, NULL),
('/user-management/user/update', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('/user-management/user/view', 3, NULL, NULL, NULL, 1643795416, 1643795416, NULL),
('Admin', 1, 'Admin', NULL, NULL, 1643795416, 1643795416, NULL),
('adminPermissions', 2, 'Admin Permissions', NULL, NULL, 1643818537, 1643877686, 'adminGroup'),
('assignRolesToUsers', 2, 'Assign roles to users', NULL, NULL, 1643795416, 1643795416, 'userManagement'),
('bindUserToIp', 2, 'Bind user to IP', NULL, NULL, 1643795416, 1643795416, 'userManagement'),
('changeOwnPassword', 2, 'Change own password', NULL, NULL, 1643795416, 1643795416, 'userCommonPermissions'),
('changeUserPassword', 2, 'Change user password', NULL, NULL, 1643795416, 1643795416, 'userManagement'),
('commonPermission', 2, 'Common permission', NULL, NULL, 1643795415, 1643795415, NULL),
('createUsers', 2, 'Create users', NULL, NULL, 1643795416, 1643795416, 'userManagement'),
('deleteUsers', 2, 'Delete users', NULL, NULL, 1643795416, 1643795416, 'userManagement'),
('editUserEmail', 2, 'Edit user email', NULL, NULL, 1643795416, 1643795416, 'userManagement'),
('editUsers', 2, 'Edit users', NULL, NULL, 1643795416, 1643795416, 'userManagement'),
('Rater', 1, 'Rater', NULL, NULL, 1643819657, 1643819657, NULL),
('raterPermissions', 2, 'Rater Permissions', NULL, NULL, 1643819684, 1643877670, 'raterGroup'),
('User', 1, 'User', NULL, NULL, 1643879565, 1643879565, NULL),
('userGroup', 2, 'User Group', NULL, NULL, 1643877822, 1643877822, 'userGroup'),
('viewRegistrationIp', 2, 'View registration IP', NULL, NULL, 1643795416, 1643795416, 'userManagement'),
('viewUserEmail', 2, 'View user email', NULL, NULL, 1643795416, 1643795416, 'userManagement'),
('viewUserRoles', 2, 'View user roles', NULL, NULL, 1643795416, 1643795416, 'userManagement'),
('viewUsers', 2, 'View users', NULL, NULL, 1643795416, 1643795416, 'userManagement'),
('viewVisitLog', 2, 'View visit log', NULL, NULL, 1643795416, 1643795416, 'userManagement');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `auth_item_child`
--

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Άδειασμα δεδομένων του πίνακα `auth_item_child`
--

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('Admin', 'adminPermissions'),
('Admin', 'assignRolesToUsers'),
('Admin', 'changeOwnPassword'),
('Admin', 'changeUserPassword'),
('Admin', 'createUsers'),
('Admin', 'deleteUsers'),
('Admin', 'editUsers'),
('Admin', 'Rater'),
('Admin', 'raterPermissions'),
('Admin', 'userGroup'),
('Admin', 'viewUsers'),
('adminPermissions', '/site/about'),
('adminPermissions', '/site/badges-create'),
('adminPermissions', '/site/captcha'),
('adminPermissions', '/site/contact'),
('adminPermissions', '/site/error'),
('adminPermissions', '/site/index'),
('adminPermissions', '/site/invite-user'),
('adminPermissions', '/site/leaderboard'),
('adminPermissions', '/site/my-surveys-view'),
('adminPermissions', '/site/participants-invite'),
('adminPermissions', '/site/questions-create'),
('adminPermissions', '/site/request-participation'),
('adminPermissions', '/site/resource-create'),
('adminPermissions', '/site/survey-create'),
('adminPermissions', '/site/survey-create-new'),
('adminPermissions', '/site/survey-delete'),
('adminPermissions', '/site/survey-overview'),
('adminPermissions', '/site/survey-participants'),
('adminPermissions', '/site/survey-rate'),
('adminPermissions', '/site/surveys-statistics'),
('adminPermissions', '/site/surveys-view'),
('adminPermissions', '/site/user-requests'),
('adminPermissions', '/user-management/auth-item-group/*'),
('adminPermissions', '/user-management/auth/*'),
('adminPermissions', '/user-management/permission/*'),
('adminPermissions', '/user-management/role/bulk-activate'),
('adminPermissions', '/user-management/role/bulk-deactivate'),
('adminPermissions', '/user-management/role/bulk-delete'),
('adminPermissions', '/user-management/role/create'),
('adminPermissions', '/user-management/role/delete'),
('adminPermissions', '/user-management/role/grid-page-size'),
('adminPermissions', '/user-management/role/grid-sort'),
('adminPermissions', '/user-management/role/index'),
('adminPermissions', '/user-management/role/set-child-permissions'),
('adminPermissions', '/user-management/role/set-child-roles'),
('adminPermissions', '/user-management/role/toggle-attribute'),
('adminPermissions', '/user-management/role/update'),
('adminPermissions', '/user-management/role/view'),
('adminPermissions', '/user-management/user-permission/set'),
('adminPermissions', '/user-management/user-permission/set-roles'),
('adminPermissions', '/user-management/user-visit-log/*'),
('adminPermissions', '/user-management/user/*'),
('adminPermissions', 'assignRolesToUsers'),
('adminPermissions', 'bindUserToIp'),
('adminPermissions', 'changeOwnPassword'),
('adminPermissions', 'changeUserPassword'),
('adminPermissions', 'createUsers'),
('adminPermissions', 'deleteUsers'),
('adminPermissions', 'editUserEmail'),
('adminPermissions', 'editUsers'),
('adminPermissions', 'raterPermissions'),
('adminPermissions', 'viewRegistrationIp'),
('adminPermissions', 'viewUserEmail'),
('adminPermissions', 'viewUserRoles'),
('adminPermissions', 'viewUsers'),
('adminPermissions', 'viewVisitLog'),
('assignRolesToUsers', '/user-management/user-permission/set'),
('assignRolesToUsers', '/user-management/user-permission/set-roles'),
('assignRolesToUsers', 'viewUserRoles'),
('assignRolesToUsers', 'viewUsers'),
('changeOwnPassword', '/user-management/auth/change-own-password'),
('changeUserPassword', '/user-management/user/change-password'),
('changeUserPassword', 'viewUsers'),
('commonPermission', '/site/about'),
('commonPermission', '/site/index'),
('commonPermission', '/site/logout'),
('commonPermission', '/user-management/auth/*'),
('createUsers', '/user-management/user/create'),
('createUsers', 'viewUsers'),
('deleteUsers', '/user-management/user/bulk-delete'),
('deleteUsers', '/user-management/user/delete'),
('deleteUsers', 'viewUsers'),
('editUserEmail', 'viewUserEmail'),
('editUsers', '/user-management/user/bulk-activate'),
('editUsers', '/user-management/user/bulk-deactivate'),
('editUsers', '/user-management/user/update'),
('editUsers', 'viewUsers'),
('Rater', 'raterPermissions'),
('raterPermissions', '/site/about'),
('raterPermissions', '/site/contact'),
('raterPermissions', '/site/index'),
('raterPermissions', '/site/login'),
('raterPermissions', '/site/logout'),
('raterPermissions', '/site/my-surveys-view'),
('raterPermissions', '/site/request-participation'),
('raterPermissions', '/site/survey-rate'),
('raterPermissions', '/site/surveys-view'),
('raterPermissions', '/user-management/auth/*'),
('raterPermissions', 'changeOwnPassword'),
('User', 'changeOwnPassword'),
('User', 'userGroup'),
('userGroup', '/site/about'),
('userGroup', '/site/captcha'),
('userGroup', '/site/contact'),
('userGroup', '/site/index'),
('userGroup', '/site/surveys-view'),
('userGroup', '/user-management/auth/change-own-password'),
('userGroup', '/user-management/auth/password-recovery'),
('userGroup', '/user-management/auth/password-recovery-receive'),
('userGroup', '/user-management/auth/registration'),
('userGroup', '/user-management/user/update'),
('viewUsers', '/user-management/user/grid-page-size'),
('viewUsers', '/user-management/user/index'),
('viewUsers', '/user-management/user/view');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `auth_item_group`
--

CREATE TABLE `auth_item_group` (
  `code` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Άδειασμα δεδομένων του πίνακα `auth_item_group`
--

INSERT INTO `auth_item_group` (`code`, `name`, `created_at`, `updated_at`) VALUES
('adminGroup', 'Admin Group', 1643877650, 1643877650),
('raterGroup', 'Rater Group', 1643877661, 1643877661),
('userCommonPermissions', 'User common permission', 1643795416, 1643795416),
('userGroup', 'User Group', 1643877790, 1643877790),
('userManagement', 'User management', 1643795416, 1643795416);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `auth_rule`
--

CREATE TABLE `auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` text DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `badges`
--

CREATE TABLE `badges` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ownerid` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `allowusers` tinyint(1) DEFAULT 1,
  `image` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `collection`
--

CREATE TABLE `collection` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `userid` int(11) DEFAULT NULL,
  `about` text DEFAULT NULL,
  `allowusers` tinyint(1) DEFAULT 1,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `fields`
--

CREATE TABLE `fields` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `invitations`
--

CREATE TABLE `invitations` (
  `id` int(11) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `surveyid` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `leaderboard`
--

CREATE TABLE `leaderboard` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `surveyid` int(11) NOT NULL,
  `points` int(10) NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `participatesin`
--

CREATE TABLE `participatesin` (
  `id` int(11) NOT NULL,
  `surveyid` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `owner` int(1) DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `request` int(11) NOT NULL DEFAULT 1,
  `finished` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `ownerid` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `question` varchar(255) NOT NULL,
  `tooltip` varchar(255) DEFAULT NULL,
  `answer` varchar(255) DEFAULT '',
  `answertype` varchar(20) NOT NULL,
  `answervalues` longtext DEFAULT NULL CHECK (json_valid(`answervalues`)),
  `allowusers` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `rate`
--

CREATE TABLE `rate` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `surveyid` int(11) NOT NULL,
  `resourceid` int(11) NOT NULL,
  `questionid` int(11) NOT NULL,
  `collectionid` int(11) NOT NULL,
  `answer` text DEFAULT NULL,
  `answertype` varchar(20) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL,
  `ownerid` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` varchar(20) NOT NULL,
  `text` text DEFAULT NULL,
  `title` varchar(500) DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `image` longblob DEFAULT NULL,
  `pmc` varchar(40) DEFAULT NULL,
  `doi` varchar(40) DEFAULT NULL,
  `pubmed_id` int(11) DEFAULT NULL,
  `authors` text DEFAULT NULL,
  `journal` varchar(100) DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `allowusers` tinyint(1) DEFAULT 0,
  `collectionid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `surveys`
--

CREATE TABLE `surveys` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `starts` timestamp NULL DEFAULT NULL,
  `ends` timestamp NULL DEFAULT NULL,
  `locked` int(1) DEFAULT 0,
  `about` text NOT NULL,
  `minRespPerRes` int(11) DEFAULT NULL,
  `maxRespPerRes` int(11) DEFAULT NULL,
  `minResEv` int(11) DEFAULT NULL,
  `maxResEv` int(11) DEFAULT NULL,
  `fields` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `badgesused` tinyint(1) NOT NULL DEFAULT 0,
  `completed` tinyint(1) NOT NULL DEFAULT 0
) ;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `surveytobadges`
--

CREATE TABLE `surveytobadges` (
  `id` int(11) NOT NULL,
  `surveyid` int(11) DEFAULT NULL,
  `badgeid` int(11) DEFAULT NULL,
  `ownerid` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `ratecondition` int(11) DEFAULT 0,
  `surveycondition` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `surveytocollections`
--

CREATE TABLE `surveytocollections` (
  `id` int(11) NOT NULL,
  `surveyid` int(11) DEFAULT NULL,
  `ownerid` int(11) DEFAULT NULL,
  `collectionid` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `surveytoquestions`
--

CREATE TABLE `surveytoquestions` (
  `id` int(11) NOT NULL,
  `surveyid` int(11) DEFAULT NULL,
  `ownerid` int(11) DEFAULT NULL,
  `questionid` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `surveytoresources`
--

CREATE TABLE `surveytoresources` (
  `id` int(11) NOT NULL,
  `surveyid` int(11) DEFAULT NULL,
  `ownerid` int(11) DEFAULT NULL,
  `resourceid` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `confirmation_token` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `superadmin` smallint(6) DEFAULT 0,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `registration_ip` varchar(15) DEFAULT NULL,
  `bind_to_ip` varchar(255) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `email_confirmed` smallint(1) NOT NULL DEFAULT 0,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `fields` text NOT NULL,
  `orcidid` varchar(19) DEFAULT NULL,
  `availability` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Άδειασμα δεδομένων του πίνακα `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `confirmation_token`, `status`, `superadmin`, `created_at`, `updated_at`, `registration_ip`, `bind_to_ip`, `email`, `email_confirmed`, `name`, `surname`, `fields`, `orcidid`, `availability`) VALUES
(1, 'superadmin', '1nIM-Vq57uEw2C4VrMuYVrcX_u4tOMMT', '$2y$13$Z0JPtazBC263YU4x4h2M7OP3qBgoX1gWhJgp43cAPc3g.d0lgox2i', NULL, 1, 1, 1643795415, 1643795415, NULL, NULL, NULL, 0, '', '', '', '', 1);
-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `usertobadges`
--

CREATE TABLE `usertobadges` (
  `id` int(11) NOT NULL,
  `surveyid` int(11) DEFAULT NULL,
  `badgeid` int(11) DEFAULT NULL,
  `userid` int(11) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `user_visit_log`
--

CREATE TABLE `user_visit_log` (
  `id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `language` char(2) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `visit_time` int(11) NOT NULL,
  `browser` varchar(30) DEFAULT NULL,
  `os` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Ευρετήρια για άχρηστους πίνακες
--

--
-- Ευρετήρια για πίνακα `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD PRIMARY KEY (`item_name`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Ευρετήρια για πίνακα `auth_item`
--
ALTER TABLE `auth_item`
  ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name` (`rule_name`),
  ADD KEY `idx-auth_item-type` (`type`),
  ADD KEY `fk_auth_item_group_code` (`group_code`);

--
-- Ευρετήρια για πίνακα `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child` (`child`);

--
-- Ευρετήρια για πίνακα `auth_item_group`
--
ALTER TABLE `auth_item_group`
  ADD PRIMARY KEY (`code`);

--
-- Ευρετήρια για πίνακα `auth_rule`
--
ALTER TABLE `auth_rule`
  ADD PRIMARY KEY (`name`);

--
-- Ευρετήρια για πίνακα `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `badges_ibfk_1` (`ownerid`);

--
-- Ευρετήρια για πίνακα `collection`
--
ALTER TABLE `collection`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collection_ibfk_1` (`userid`);

--
-- Ευρετήρια για πίνακα `fields`
--
ALTER TABLE `fields`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Ευρετήρια για πίνακα `invitations`
--
ALTER TABLE `invitations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hash` (`hash`),
  ADD KEY `invitations_ibfk_1` (`surveyid`);

--
-- Ευρετήρια για πίνακα `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leaderboard_ibfk_1` (`userid`),
  ADD KEY `leaderboard_ibfk_2` (`surveyid`);

--
-- Ευρετήρια για πίνακα `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Ευρετήρια για πίνακα `participatesin`
--
ALTER TABLE `participatesin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `participatesIn_ibfk_1` (`userid`),
  ADD KEY `participatesIn_ibfk_2` (`surveyid`);

--
-- Ευρετήρια για πίνακα `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_ibfk_1` (`ownerid`);

--
-- Ευρετήρια για πίνακα `rate`
--
ALTER TABLE `rate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rate_ibfk_1` (`userid`),
  ADD KEY `rate_ibfk_2` (`surveyid`),
  ADD KEY `rate_ibfk_3` (`resourceid`),
  ADD KEY `rate_ibfk_4` (`questionid`),
  ADD KEY `rate_ibfk_5` (`collectionid`);

--
-- Ευρετήρια για πίνακα `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resource_ibfk_1` (`ownerid`),
  ADD KEY `resource_ibfk_2` (`collectionid`);

--
-- Ευρετήρια για πίνακα `surveys`
--
ALTER TABLE `surveys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Ευρετήρια για πίνακα `surveytobadges`
--
ALTER TABLE `surveytobadges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `SurveyToBadges_ibfk_1` (`ownerid`),
  ADD KEY `SurveyToBadges_ibfk_2` (`badgeid`),
  ADD KEY `SurveyToBadges_ibfk_3` (`surveyid`);

--
-- Ευρετήρια για πίνακα `surveytocollections`
--
ALTER TABLE `surveytocollections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surveyToCollections_ibfk_1` (`ownerid`),
  ADD KEY `surveyToCollections_ibfk_2` (`surveyid`),
  ADD KEY `surveyToCollections_ibfk_3` (`collectionid`);

--
-- Ευρετήρια για πίνακα `surveytoquestions`
--
ALTER TABLE `surveytoquestions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surveyToQuestions_ibfk_1` (`ownerid`),
  ADD KEY `surveyToQuestions_ibfk_2` (`surveyid`),
  ADD KEY `surveyToQuestions_ibfk_3` (`questionid`);

--
-- Ευρετήρια για πίνακα `surveytoresources`
--
ALTER TABLE `surveytoresources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surveyToResources_ibfk_1` (`ownerid`),
  ADD KEY `surveyToResources_ibfk_2` (`surveyid`),
  ADD KEY `surveyToResources_ibfk_3` (`resourceid`);

--
-- Ευρετήρια για πίνακα `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Ευρετήρια για πίνακα `usertobadges`
--
ALTER TABLE `usertobadges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userHasBadges_ibfk_1` (`userid`),
  ADD KEY `userHasBadges_ibfk_2` (`badgeid`),
  ADD KEY `userHasBadges_ibfk_3` (`surveyid`);

--
-- Ευρετήρια για πίνακα `user_visit_log`
--
ALTER TABLE `user_visit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT για άχρηστους πίνακες
--

--
-- AUTO_INCREMENT για πίνακα `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `collection`
--
ALTER TABLE `collection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `fields`
--
ALTER TABLE `fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `invitations`
--
ALTER TABLE `invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `leaderboard`
--
ALTER TABLE `leaderboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `participatesin`
--
ALTER TABLE `participatesin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `rate`
--
ALTER TABLE `rate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `resources`
--
ALTER TABLE `resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `surveys`
--
ALTER TABLE `surveys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `surveytobadges`
--
ALTER TABLE `surveytobadges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `surveytocollections`
--
ALTER TABLE `surveytocollections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `surveytoquestions`
--
ALTER TABLE `surveytoquestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `surveytoresources`
--
ALTER TABLE `surveytoresources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `usertobadges`
--
ALTER TABLE `usertobadges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT για πίνακα `user_visit_log`
--
ALTER TABLE `user_visit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Περιορισμοί για άχρηστους πίνακες
--

--
-- Περιορισμοί για πίνακα `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_assignment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_auth_item_group_code` FOREIGN KEY (`group_code`) REFERENCES `auth_item_group` (`code`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `badges`
--
ALTER TABLE `badges`
  ADD CONSTRAINT `badges_ibfk_1` FOREIGN KEY (`ownerid`) REFERENCES `user` (`id`);

--
-- Περιορισμοί για πίνακα `collection`
--
ALTER TABLE `collection`
  ADD CONSTRAINT `collection_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`id`);

--
-- Περιορισμοί για πίνακα `invitations`
--
ALTER TABLE `invitations`
  ADD CONSTRAINT `invitations_ibfk_1` FOREIGN KEY (`surveyid`) REFERENCES `surveys` (`id`);

--
-- Περιορισμοί για πίνακα `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD CONSTRAINT `leaderboard_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `leaderboard_ibfk_2` FOREIGN KEY (`surveyid`) REFERENCES `surveys` (`id`);

--
-- Περιορισμοί για πίνακα `participatesin`
--
ALTER TABLE `participatesin`
  ADD CONSTRAINT `participatesIn_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `participatesIn_ibfk_2` FOREIGN KEY (`surveyid`) REFERENCES `surveys` (`id`);

--
-- Περιορισμοί για πίνακα `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`ownerid`) REFERENCES `user` (`id`);

--
-- Περιορισμοί για πίνακα `rate`
--
ALTER TABLE `rate`
  ADD CONSTRAINT `rate_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `rate_ibfk_2` FOREIGN KEY (`surveyid`) REFERENCES `surveys` (`id`),
  ADD CONSTRAINT `rate_ibfk_3` FOREIGN KEY (`resourceid`) REFERENCES `resources` (`id`),
  ADD CONSTRAINT `rate_ibfk_4` FOREIGN KEY (`questionid`) REFERENCES `questions` (`id`),
  ADD CONSTRAINT `rate_ibfk_5` FOREIGN KEY (`collectionid`) REFERENCES `collection` (`id`);

--
-- Περιορισμοί για πίνακα `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `resource_ibfk_1` FOREIGN KEY (`ownerid`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `resource_ibfk_2` FOREIGN KEY (`collectionid`) REFERENCES `collection` (`id`);

--
-- Περιορισμοί για πίνακα `surveytobadges`
--
ALTER TABLE `surveytobadges`
  ADD CONSTRAINT `SurveyToBadges_ibfk_1` FOREIGN KEY (`ownerid`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `SurveyToBadges_ibfk_2` FOREIGN KEY (`badgeid`) REFERENCES `badges` (`id`),
  ADD CONSTRAINT `SurveyToBadges_ibfk_3` FOREIGN KEY (`surveyid`) REFERENCES `surveys` (`id`);

--
-- Περιορισμοί για πίνακα `surveytocollections`
--
ALTER TABLE `surveytocollections`
  ADD CONSTRAINT `surveyToCollections_ibfk_1` FOREIGN KEY (`ownerid`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `surveyToCollections_ibfk_2` FOREIGN KEY (`surveyid`) REFERENCES `surveys` (`id`),
  ADD CONSTRAINT `surveyToCollections_ibfk_3` FOREIGN KEY (`collectionid`) REFERENCES `collection` (`id`);

--
-- Περιορισμοί για πίνακα `surveytoquestions`
--
ALTER TABLE `surveytoquestions`
  ADD CONSTRAINT `surveyToQuestions_ibfk_1` FOREIGN KEY (`ownerid`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `surveyToQuestions_ibfk_2` FOREIGN KEY (`surveyid`) REFERENCES `surveys` (`id`),
  ADD CONSTRAINT `surveyToQuestions_ibfk_3` FOREIGN KEY (`questionid`) REFERENCES `questions` (`id`);

--
-- Περιορισμοί για πίνακα `surveytoresources`
--
ALTER TABLE `surveytoresources`
  ADD CONSTRAINT `surveyToResources_ibfk_1` FOREIGN KEY (`ownerid`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `surveyToResources_ibfk_2` FOREIGN KEY (`surveyid`) REFERENCES `surveys` (`id`),
  ADD CONSTRAINT `surveyToResources_ibfk_3` FOREIGN KEY (`resourceid`) REFERENCES `resources` (`id`);

--
-- Περιορισμοί για πίνακα `usertobadges`
--
ALTER TABLE `usertobadges`
  ADD CONSTRAINT `userHasBadges_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `userHasBadges_ibfk_2` FOREIGN KEY (`badgeid`) REFERENCES `badges` (`id`),
  ADD CONSTRAINT `userHasBadges_ibfk_3` FOREIGN KEY (`surveyid`) REFERENCES `surveys` (`id`);

--
-- Περιορισμοί για πίνακα `user_visit_log`
--
ALTER TABLE `user_visit_log`
  ADD CONSTRAINT `user_visit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
