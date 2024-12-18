-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2024-12-18 10:44:52
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `test1`
--

-- --------------------------------------------------------

--
-- 資料表結構 `case information`
--

CREATE TABLE `case information` (
  `id` int(11) NOT NULL,
  `case_name` varchar(255) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `client_age` int(11) NOT NULL,
  `client_contact` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `case information`
--

INSERT INTO `case information` (`id`, `case_name`, `client_name`, `client_age`, `client_contact`, `created_at`) VALUES
(1, '骨折', '水同學', 21, '0326561151', '2024-12-08 11:07:38'),
(2, 'njngf', 'fgbfd', 56, '068563156', '2024-12-08 11:08:00'),
(3, 'football', 'messi', 39, '25700145365', '2024-12-17 09:37:52'),
(4, 'david', 'david', 25, '0912345678', '2024-12-17 10:05:58'),
(5, '市川', '市川', 30, '0923456789', '2024-12-17 10:05:58'),
(6, '頭痛', '小名', 5, '036977451', '2024-12-18 09:38:29');

-- --------------------------------------------------------

--
-- 資料表結構 `cases`
--

CREATE TABLE `cases` (
  `id` int(11) NOT NULL,
  `case_name` varchar(100) DEFAULT NULL,
  `social_worker_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `cases`
--

INSERT INTO `cases` (`id`, `case_name`, `social_worker_id`, `created_at`, `status`) VALUES
(2, '市川', 4, '2024-12-08 10:37:04', 1),
(3, 'david', 2, '2024-12-08 10:38:38', 1),
(4, '骨折', 3, '2024-12-08 11:07:38', 1),
(5, 'njngf', NULL, '2024-12-08 11:08:00', 1),
(7, 'football', 0, '2024-12-17 09:37:52', 1),
(8, '頭痛', NULL, '2024-12-18 09:38:29', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `interview_records`
--

CREATE TABLE `interview_records` (
  `id` int(11) NOT NULL,
  `case_id` int(11) DEFAULT NULL,
  `interview_date` date DEFAULT NULL,
  `record` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `interview_records`
--

INSERT INTO `interview_records` (`id`, `case_id`, `interview_date`, `record`) VALUES
(3, 2, '2024-12-08', '5615482231'),
(4, 3, '2024-12-16', 'i think fpf[dk hnbjmoibh pokgkbm emldcpv'),
(5, 7, '2024-12-17', 'mrbom rmn, d mjlnqeoij '),
(6, 4, '2024-12-18', 'n ,l;, lgmfkvmfkdewg eth');

-- --------------------------------------------------------

--
-- 資料表結構 `social_workers`
--

CREATE TABLE `social_workers` (
  `id` int(11) NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_roman_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_roman_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_roman_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_roman_ci NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `social_workers`
--

INSERT INTO `social_workers` (`id`, `username`, `password`, `name`, `email`, `is_admin`) VALUES
(0, 'admin', '$2y$10$JMJCYzDILaWD3pFbwW4yKek8u3guxxXoinD5LVuqzeTqxlCK2pM6G', '管理員', 'admin@example.com', 1),
(1, 'roy', '$2y$10$JMJCYzDILaWD3pFbwW4yKek8u3guxxXoinD5LVuqzeTqxlCK2pM6G', 'Roy', 'roy@example.com', 1),
(2, '豬豬寶貝', '$2y$10$qimInHkX7YYdKksESiysI.KwWJgQSmWP9meXtigwkZ98Pvcs8aDlu', '統神', '412401501@m365.fju.edu.tw', 1),
(3, '傑寶', '$2y$10$nT5WD79k7sdWK75/Rtnli.4pFz9wTO.jhj7S6DOIqoluZyAJ0h8HW', '羅傑', '412401501@m365.fju.edu.tw', 1),
(4, '山田', '$2y$10$aGhpypD5zRK6dszVKy15sem8R.pjhi.URRB//roS3vcQ7/yB23EGu', '杏奈奈', '4753@gmail.com', 1);

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `case information`
--
ALTER TABLE `case information`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `cases`
--
ALTER TABLE `cases`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `interview_records`
--
ALTER TABLE `interview_records`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `social_workers`
--
ALTER TABLE `social_workers`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `case information`
--
ALTER TABLE `case information`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `cases`
--
ALTER TABLE `cases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `interview_records`
--
ALTER TABLE `interview_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `social_workers`
--
ALTER TABLE `social_workers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
