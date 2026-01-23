-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2026 at 04:37 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `grumpyhare__wp_dfy25`
--

-- --------------------------------------------------------

--
-- Table structure for table `facebook_ads_data`
--

CREATE TABLE `facebook_ads_data` (
  `id` int(10) UNSIGNED NOT NULL,
  `report_date` date NOT NULL,
  `raw_row` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`raw_row`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `facebook_ads_data`
--

INSERT INTO `facebook_ads_data` (`id`, `report_date`, `raw_row`, `created_at`, `updated_at`) VALUES
(25, '2025-12-15', '[[\"SEO - DFY - NEW LINES\",\"DFY-BKN420 | I Hear It All The Time | 3 minutes BPCON2021, Alex Review Video |  Lookalike(US,3%-4%)-340-ROWS  ? 09\\/30 ?\",\"DFY-BKN420 | I Hear It All The Time | 3 minutes BPCON2021 |  Lookalike(US,3%-4%)-340-ROWS  ? 09\\/30\",\"active\",\"ad\",\"104000\",\"28877\",\"2.26220133\",\"Website applications submitted\",\"500\",\"246.28\",\"5000\",\"9\\/30\\/2025\",\"Ongoing\",\"93.8154933\",\"186\",\"200\",\"0.64411123\",\"\",\"12\\/15\\/2025\",\"12\\/30\\/2025\"],[\"SEO - DFY - NEW LINES\",\"DFY-BKN437 - Stop Chasing Motivated Seller Leads! | N1-RANKING-WEBSITE, RIDE | 5 - Property investing + Real estate investment association + Real propert Narrow Household income: top 10% (M) 07\\/09\",\"DFY-BKN437 - Stop Chasing Motivated Seller Leads! | N1-RANKING-WEBSITE, RIDE | 5 - Property investing + Real estate investment association + Real propert Narrow Household income: top 10% (M) 07\\/09\",\"active\",\"ad\",\"99999\",\"16051\",\"1.59094063\",\"Website applications submitted\",\"16\",\"132.63\",\"2122\",\"10\\/24\\/2025\",\"Ongoing\",\"132.203601\",\"187\",\"11.34759358\",\"1.16503645\",\"\",\"12\\/15\\/2025\",\"12\\/30\\/2025\"],[\"SEO - DFY - NEW LINES\",\"DFY-BKN423 | PPL Original, new lines | 3 minutes BPCON2021 - N1-RANKING-WEBSITE |  Property-investing+Real estate investment association+Real-property-Narrow-Household-income:top10%-(M)  ? 09\\/30 ?\",\"DFY-BKN423 | PPL-Original, new lines | N1-RANKING-WEBSITE - |  Property-investing+Real-estate-investment-association+Real-property-Narrow-Household-income:top10%-(M)  ? 09\\/30 ?\",\"active\",\"ad\",\"99999\",\"15844\",\"1.64033544\",\"Website applications submitted\",\"18\",\"108.63\",\"1955.34\",\"9\\/30\\/2025\",\"Ongoing\",\"123.4120172\",\"181\",\"10.80298343\",\"1.14238829\",\"\",\"12\\/15\\/2025\",\"12\\/30\\/2025\"],[\"SEO - DFY - NEW LINES\",\"DFY-BKN436 - Simply Put: new lines  | J Pointing at screen & J in KITCHEN   |1 - Property investing + Real estate Narrow Landlord (M) 24\\/10 ?\",\"DFY-BKN436 - Simply Put: new lines  | J in KITCHEN   |1 - Property investing + Real estate Narrow Landlord (M) 24\\/10 ?\",\"active\",\"ad\",\"10727\",\"21823\",\"2.03439918\",\"Website applications submitted\",\"12\",\"143.66\",\"1723.89\",\"10\\/24\\/2025\",\"Ongoing\",\"78.99418045\",\"200\",\"8.61945\",\"0.91646428\",\"\",\"12\\/15\\/2025\",\"12\\/30\\/2025\"],[\"SEO - DFY - NEW LINES\",\"DFY-BKN435 - Simply Put: new lines  | J&J- BROKEN KITCHEN | Property finder (property) (M) 24\\/10\",\"DFY-BKN435 - Simply Put: new lines  |  BROKEN KITCHEN | Property finder (property) (M) 24\\/10\",\"active\",\"ad\",\"13783\",\"25060\",\"1.81818182\",\"Website applications submitted\",\"16\",\"88.72\",\"1419.49\",\"10\\/24\\/2025\",\"Ongoing\",\"56.64365523\",\"330\",\"4.30148485\",\"1.31683958\",\"\",\"12\\/15\\/2025\",\"12\\/30\\/2025\"],[\"SEO - DFY - NEW LINES\",\"DFY-BKN435 - Simply Put: new lines  | J&J- BROKEN KITCHEN | Property finder (property) (M) 24\\/10\",\"DFY-BKN435 - Simply Put: new lines  | J&J- | Property finder (property) (M) 24\\/10\",\"active\",\"ad\",\"6717\",\"13467\",\"2.00491291\",\"Website applications submitted\",\"10\",\"69.38\",\"693.84\",\"10\\/24\\/2025\",\"Ongoing\",\"51.52149699\",\"221\",\"3.13954751\",\"1.64104849\",\"\",\"12\\/15\\/2025\",\"12\\/30\\/2025\"],[\"SEO - DFY - NEW LINES\",\"DFY-BKN436 - Simply Put: new lines  | J Pointing at screen & J in KITCHEN   |1 - Property investing + Real estate Narrow Landlord (M) 24\\/10 ?\",\"DFY-BKN436 - Simply Put: new lines  | J Pointing at screen  |1 - Property investing + Real estate Narrow Landlord (M) 24\\/10 ?\",\"active\",\"ad\",\"2719\",\"4933\",\"1.81426995\",\"Website applications submitted\",\"2\",\"194.1\",\"388.2\",\"10\\/24\\/2025\",\"Ongoing\",\"78.69450639\",\"45\",\"8.62666667\",\"0.9122238\",\"\",\"12\\/15\\/2025\",\"12\\/30\\/2025\"],[\"SEO - DFY - NEW LINES\",\"DFY-BKN420 | I Hear It All The Time | 3 minutes BPCON2021, Alex Review Video |  Lookalike(US,3%-4%)-340-ROWS  ? 09\\/30 ?\",\"DFY-BKN420 | I Hear It All The Time |  Alex-Review-Video |  Lookalike(US,3%-4%)-340-ROWS ? 09\\/30 ? Copy\",\"active\",\"ad\",\"1914\",\"3763\",\"1.96603971\",\"Website applications submitted\",\"4\",\"83.59\",\"334.34\",\"9\\/30\\/2025\",\"Ongoing\",\"88.84932235\",\"25\",\"13.3736\",\"0.66436354\",\"\",\"12\\/15\\/2025\",\"12\\/30\\/2025\"]]', '2026-01-16 19:17:22', '2026-01-16 19:17:22'),
(26, '2025-12-16', '[[\"SEO - DFY - NEW LINES\",\"DFY-BKN423 | PPL Original, new lines | 3 minutes BPCON2021 - N1-RANKING-WEBSITE |  Property-investing+Real estate investment association+Real-property-Narrow-Household-income:top10%-(M)  ? 09\\/30 ?\",\"DFY-BKN423 | PPL-Original, new lines | N1-RANKING-WEBSITE - |  Property-investing+Real-estate-investment-association+Real-property-Narrow-Household-income:top10%-(M)  ? 09\\/30 ?\",\"active\",\"ad\",\"1021\",\"1382\",\"1.35357493\",\"\",\"\",\"\",\"153.09\",\"9\\/30\\/2025\",\"Ongoing\",\"110.7742402\",\"8\",\"19.13625\",\"0.5788712\",\"\",\"12\\/16\\/2025\",\"12\\/30\\/2025\"]]', '2026-01-16 19:17:22', '2026-01-16 19:17:22');

-- --------------------------------------------------------

--
-- Table structure for table `facebook_reports`
--

CREATE TABLE `facebook_reports` (
  `id` int(11) NOT NULL,
  `report_start` date NOT NULL,
  `report_end` date NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facebook_report_headers`
--

CREATE TABLE `facebook_report_headers` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `header_name` varchar(255) NOT NULL,
  `header_index` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facebook_report_rows`
--

CREATE TABLE `facebook_report_rows` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `row_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`row_data`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `facebook_ads_data`
--
ALTER TABLE `facebook_ads_data`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_report_date` (`report_date`),
  ADD KEY `idx_report_date` (`report_date`);

--
-- Indexes for table `facebook_reports`
--
ALTER TABLE `facebook_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `facebook_report_headers`
--
ALTER TABLE `facebook_report_headers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_id` (`report_id`);

--
-- Indexes for table `facebook_report_rows`
--
ALTER TABLE `facebook_report_rows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_id` (`report_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `facebook_ads_data`
--
ALTER TABLE `facebook_ads_data`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `facebook_reports`
--
ALTER TABLE `facebook_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `facebook_report_headers`
--
ALTER TABLE `facebook_report_headers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=316;

--
-- AUTO_INCREMENT for table `facebook_report_rows`
--
ALTER TABLE `facebook_report_rows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `facebook_report_headers`
--
ALTER TABLE `facebook_report_headers`
  ADD CONSTRAINT `facebook_report_headers_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `facebook_reports` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `facebook_report_rows`
--
ALTER TABLE `facebook_report_rows`
  ADD CONSTRAINT `facebook_report_rows_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `facebook_reports` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
