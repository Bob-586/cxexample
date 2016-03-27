-- phpMyAdmin SQL Dump
-- version 4.5.0.2
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Mar 25, 2016 at 10:52 AM
-- Server version: 5.6.29-76.2
-- PHP Version: 7.0.4-7+deb.sury.org~trusty+2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `main`
--

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `access` int(10) UNSIGNED DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `access`, `data`) VALUES
('0i0r6j2ialuoolueglt67it822', 1458917395, 'TFjUiMSIE2PlNsbgOqMpeb2UmXk/S/derZaFkawAUluYdO9HepHQ1HhhitlVrXvlQbY3prFxNNwIBcVxyT1+dYSuZNwJGib9jrOqCk26ps1L4VZJ1oQZ7kPCU4FsFgOuER57Ixzq9VXiWMXaOErEHm2xkZRBm3MDYZ5AByOkV6GwCesaYWa9i1y2xA/FeuOuAkom3xoieigEzSDQ/Zs1ZMWvXsHUY4LH7A5TDUZiKLMMc4zviMHfyXTo5cuTDIWBlAMWP/9oe/TH9TtfiH1wV0FrI1TZ0ccDSD4Uyag7bex01xt0fzAbSuR8GcsslmavmIJpvHL/aUnEpsDVW8JOYv0Sl2UhhHF5V+E8BQuFyI4OXaBhULTH5eFSvH/modrPTokGS+BtMic0SMrry8zLmA+fbRVIdWfjePtrycOsmBho0WMX+wRkKF0I44uKPa+HvzL6JIFfBQWnUiR61qw6M4loJfD+okMYhWPB39ApFopsgwpaNwPhZ3IyQEHBCpDpiNIMnAKtkXLMtCmKLwe15HXdIraIwpw+'),
('0pcfb1a75tc6cfhq8dvmqtvm21', 1458872306, 'GaeIKGu+cy5Nfu5mf3s9ADaQmOrpXmEIbJK8shVqhRLNKS4/z0EAogfHikOeXefHgkvzdWm6X2S0KTmcrcSC6Y+KBFA2fbrX4+3krpOfNa3popHMUsyFz4+eSpPxc9ZbdfHZxRZRvH9KPc30W94CPBE3eGb/zO/3k2cL5sygwMA8FeDC9slaHSXLAbbmHfxLxEdgt/aPhhgasbDhjUypyUEOKVL+9+3RizpJScD/UGtPNxiA7Y2jdk7OaDzO7GrhQKIv+vw6ONoehoYvectp2+iUQQSwCA2luGGoIYllA2tymQbZUT+KA99b/QMZNfZbwLbM0bU3hhtqGb14HTXKyI/XwRYP+xvr7BU2HvDTJs0tuUkLkV9dZoDEnwBpeLGa/1YvnnPo+vnRsp5j9brDM056+wOLgJxkp9a/ZuIZVqJe+Dpi+GYsdb/uykfeOkwh8skN6ji3tFl/o/ePdqorfAxTE/gzbyZf1j5eGs8ga+o='),
('1ppf5slbla598dh4tqce7pttt0', 1458869142, 'x4gq+upcxbH42l8SuwLXjD+vxp567I32WLUvBhPrknqbieYIx5IXNt6XhPcX7Nr4M7HHMi5q1gmXzkiDpHmOpFcWJsaH9oIQha5Jk2/lXoLlrAPPGhpdZV0pAhS2BKcFx7NE36EH6AqCkb3fTx1RUv41I/16ckh0a3qskfV49WKXXc7Jy0hC85pBuuIrM+TjHV5PCVWLTrriLHXmy5qF0Joj/Rl0OozZfyOlBcmM5ySyhkvRaJw/pqRiJlbbaUgwkWqLNkV49YPRCJAKTKmF6G85iy2imVyOTzYzUIOn1nAv+qZ8yyGu1H4RSe5qmk1eEv8RL4ECYLser2UJAAi7+meoqO1H89Jc4bpaKMcNCfq9Zf9Pzlgetzvid1MkJtPu/8tQ3agFxzHyqggqxHPTh/YD98wCftJTdB9wxDlyhPBn2nnTx6xzrF1b8m8eq3GVnwgrIoRkRYsA9VHLlv+BMQ=='),
('3k09udef4gi3u7bgd7i76h7fp1', 1458872318, 'STruFibxsZSHjMiH+2B63g=='),
('dt6u93vrd86pbn0bfb740oln62', 1458872394, '85IQpPSaGXfllJHXIYbaG270dya8uar3CrFR/goNtslY6h6wfL/J+dg1eTXZHY9oQflOwl7EbntO1Agi6dEvxcEVVnW+4HptiHwMAf3d9/DTkk189xl0xyn8VxaOPb67O/kmjIqHs+PWNODU5DVkImynbg3LFh++PnNfHgfQqbmQjvAoZ/9FuZghl7pQFSvAEERvvn/MiUJN75DY8uHveHTy92WDeNzwtBlwhMlITpVUG4GkgfQp0UG4v1fDfOUclt/WUUBLNcpZT9KPHSXzcekFpCAy1Spjk2lx/oeCFNwBVY6bO9X37xZ4S70+D2YNO05Qe7eNyxksDJeY0a0mQY/GBgvQZWoQXWyeDVyPIaJoBi9sOlplY/YKPeYWprLYsdWTtWd/L9IpDBnCvIdEeRN/Rpz+hIxY5w0NcCqPWmMJTkMtNBVt0Q8G8UN+CywbVQr4q0im8nDuQy5kaQgQRXMwtSjCdFl4Tlr1JxuoZ03cE2HzvxVfme5c8frnzeqQQ/n0Qq3iDqYWnJuvRXK2SC3yfEj9vrqn'),
('e74vmq2f1k8nn0r1bd0m87lc41', 1458860072, 'bD9P87GRFSXG+E8gy05BKg=='),
('ga1801u8r8rm3r97b2jb4e2874', 1458869592, 'UJKTkdG7vmMJBkxaXHwSvg=='),
('hrfg9mhhnefn7epugi5veg56q5', 1458873771, 'zgo6VzzLZ/JtkFXDY0YPyQ=='),
('rnltvn2d41aqfcu3qfh3luf7n5', 1458869651, 'BaHDYy3nKs83vcyFkstSeQ=='),
('uepke9r9ct8a4pqrhk4fvjf2r3', 1458869912, '30SggTvSJ5Ou2/lXxKM/1w==');

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE `test` (
  `id` int(10) UNSIGNED NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`id`, `data`) VALUES
(1, 'Hello, All! PHP Rules!'),
(2, 'Wow....');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` mediumint(20) UNSIGNED NOT NULL,
  `username` varchar(80) NOT NULL,
  `password` text NOT NULL,
  `identifier` varchar(32) DEFAULT NULL,
  `token` varchar(32) DEFAULT NULL,
  `timeout` int(11) UNSIGNED DEFAULT NULL,
  `last_failure` int(10) UNSIGNED DEFAULT NULL,
  `failures` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `fname` varchar(60) NOT NULL,
  `lname` varchar(60) NOT NULL,
  `rights` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `identifier`, `token`, `timeout`, `last_failure`, `failures`, `fname`, `lname`, `rights`) VALUES
(1, 'bob_586', '$2y$10$jf0GVdybXcO5mnspHhGV.euj9tPCwZUXfVZ0v3f043ZlZrzyhqsdO', '46b5472cccd2ccff71dd9a29069e2c57', NULL, NULL, 1458874312, 1, 'Robert', 'Strutts', 'a:2:{i:0;s:5:"admin";i:1;s:6:"office";}'),
(2, 'chris', '$2y$10$aa/uR.qwlZlIhif3Xq1QaecmmpaPzmMmWFTX0y6lx46mgmlGDIC02', 'db599fe2bd03951c95c8c99b20600394', NULL, NULL, NULL, 0, 'Chris', 'Allen', 'a:5:{i:0;s:5:"admin";i:1;s:3:"mgr";i:2;s:5:"sales";i:3;s:6:"office";i:4;s:3:"cus";}');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username_2` (`username`),
  ADD KEY `username` (`identifier`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `test`
--
ALTER TABLE `test`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` mediumint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
