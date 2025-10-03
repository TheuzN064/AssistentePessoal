-- phpMyAdmin SQL Dump
-- version 5.2.2deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 03/10/2025 às 14:03
-- Versão do servidor: 11.8.3-MariaDB-0+deb13u1 from Debian
-- Versão do PHP: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `assis_pessoal`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `bills`
--

CREATE TABLE `bills` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `due_date` date NOT NULL,
  `is_paid` tinyint(1) DEFAULT 0,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `bills`
--

INSERT INTO `bills` (`id`, `user_id`, `name`, `value`, `due_date`, `is_paid`, `paid_at`, `created_at`) VALUES
(12, 3, 'qBlXDf7aWFKlP9vVh0PGVc//IcCAW037zV6pbGXyo6dnVwMxbA==', 'wMu5top/RdYqb+fk8TvLpAAfHBY5HVTiI1YjFOGxdCap4Q==', '2025-08-17', 1, '2025-08-28 05:56:43', '2025-08-28 03:51:00'),
(13, 3, 'S0JHJyHUXTBDo1yQdd3B35x80XW7OVzawoUa00LNoaeEj8g4NQ==', 'Qzg1XRPG5YayEPwNTjQgiVFKbXBpvpA/knECD+/32e8A1A==', '2025-09-08', 1, '2025-09-18 20:05:53', '2025-08-28 03:57:32'),
(14, 3, 'wpJK7YA9CLnb5u0gRV7MHOirNwqhP9PxwXgCN5TIGqH3', '646cDBcSgsWFz5q2qqNdy7OckpaQsLkpoCRdbXCjBx0L3w==', '2025-09-08', 1, '2025-09-18 20:05:53', '2025-08-28 03:58:33'),
(15, 3, '8O+5pXnqa6sy0rkVFUztaP7r89ZAkwkoeQx27zldvddf', 'KmtJJhr97rAW/riBhORhNpZhDWjkN6HnoF3FHrhOt0MBwA==', '2025-09-08', 1, '2025-09-18 20:05:53', '2025-08-28 03:59:03'),
(16, 3, 'uf3jc827CQPWaMqzVLMg5TCDM2PbfiBLz2G+V+aPYxDAv7E4J5oRiQ==', 'ZdXmDdC9lDwfUiuvmMcytGwErQYeP68LOCMM8GOt3r+VEw==', '2025-09-06', 1, '2025-09-18 20:05:53', '2025-08-28 03:59:57'),
(17, 3, 'sKv6FIeNIVd75Po8q6lXRMni2HjZKpK0aTohfpWWDQ/3aouPdA==', 'hC83dtVXcHzKYtJwIhXjVb8D5wSo+0L7aTFCaj3vi7B6TA==', '2025-09-15', 1, '2025-09-18 20:05:53', '2025-08-28 04:00:57'),
(18, 3, '9PknK0+l+zlMAhVonrpmdC9j4CCfFZTHB9ALLCDwCGSdv35Ke+fdTBQJzw8rHFGk/BzOXw==', 'VROs+ODuCsKICyxd91cQkdNw6uUYxvrlszkHWuAQQSX0AQ==', '2025-09-10', 1, '2025-09-18 20:05:53', '2025-08-28 04:02:48'),
(19, 3, 'ESl4Yj+BtJJJDobUfMnDWhRwvhvjrtp9pR9mpicg16fiujxqM/n0', 'JBRQsxNeMSLbsKW4cyjW9TXIhUE+X/0ZddiB24say9nIOw==', '2025-09-09', 1, '2025-09-18 20:05:53', '2025-08-28 04:05:54'),
(20, 3, 'IdKr34vW79KEcaVOJ6eWZW+kNQQfeik0RAiKVk+j/BuWYvG6INSY5w==', 'kr90wAxyHkwcMHlmkOqbhLm8PzocOhCZT+7Fe9Mg5N/oXQ==', '2025-10-06', 0, NULL, '2025-08-28 04:09:49'),
(21, 3, 'zppHFCVxVmfs/eh+RwEBeUER+n4UTOPsVKGvh+QIiEhPPTsC2zP2ng==', 'ZrKLIURY/oBsPdYDrEd6L3RKB4BhZ9roN/PjL8LRW0dnRA==', '2025-11-06', 0, NULL, '2025-08-28 04:10:40'),
(22, 3, '6kNrSEDs2pw2Mh902Ti+FIiJxppb0vrl26850lQWrNhU5mkIAA1n2w==', '+XRirN8GpWcLEwYlX405bDVxV9+WlY6BU1Y4lSI3NGKLgg==', '2025-12-06', 0, NULL, '2025-08-28 04:11:01'),
(23, 3, 'X2gDMqYDwCaUpNX4j/1wbETItwXgTUVt9pGlYcRjhYH9q0ZPB2jE', 'xDm+hgy2tNifMg5AC4FfVXpOJwGNbQZ1aVqcnZpqVzJnxA==', '2025-10-09', 0, NULL, '2025-08-28 04:11:41'),
(24, 3, 'UfpvpA/gITRatMRruUngjP8RIkMP+ljGRg2NYibjnlSAbtF64+Xz', 'xjpM10tAg3c/lBFn9yDalBpQcIbmlB9TZmgIPQx4OXBiug==', '2025-11-06', 0, NULL, '2025-08-28 04:11:54'),
(25, 3, 's0d1SYgj6HX9wtlXAAsqQLgiiDRQR+S9O51vcSRtlLqrZjktYCpv', 'O92TKa+TqxZ++8yOGTkyi4ReOR5z3yE3N+fPbTcekbSOvQ==', '2025-12-09', 0, NULL, '2025-08-28 04:12:08'),
(26, 3, 'Eu1E0spf7tkQgkjwl0z0Eolxx0SO/tCikkShHG9dOe8f0CO/Kg==', 'JZTnlj4xMFhXB4BVCcrKpUlQXTfae5yhc278chiMHPr8Jg==', '2025-10-08', 0, NULL, '2025-08-28 04:16:46'),
(27, 3, 'L5ra5Zqpw9mPL1p4DAswUzX6+7X+RqImwERY6IWwXcDRHOdRhw==', '40Uj5w/yxQXqZZpsBYBXQUedVxIZcIvythT2TBNNQ2arRg==', '2025-11-08', 0, NULL, '2025-08-28 04:17:28'),
(28, 3, '3YhqlmXLS3d2AjDW6XN7PbwRZaP45u52rgKXjddqYXXuWJ6GhA==', 'WuOs9WRqJLRwGaqQrZTkKeu8/vZXxMIcEtfGgHRJ+D7dAw==', '2025-12-08', 0, NULL, '2025-08-28 04:19:04'),
(29, 5, 'vLWE8KewgyKU8vKrJXp41FQevtFxNuwW7n9D/4AM7kANRwc=', 'aHFRfbeHdAQFtLGdzrVEOdGaWQjXTLTOwu9HlMI3CNjX', '2025-09-10', 0, NULL, '2025-09-02 07:28:29'),
(30, 5, 'WFOfrD7LwKNxW/g7dp/WW+N+mfddJcCvW4xpkQiLSgB+1mU=', 'dWyXYDpdqBRTbaNFdmk/F7JpqFYFFwwlNrJ/s2fn', '2025-09-10', 0, NULL, '2025-09-02 07:28:52');

-- --------------------------------------------------------

--
-- Estrutura para tabela `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_name` text NOT NULL,
  `card_holder_name` text NOT NULL,
  `card_number` text NOT NULL,
  `expiry_date` text NOT NULL,
  `cvv` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cards`
--

INSERT INTO `cards` (`id`, `user_id`, `card_name`, `card_holder_name`, `card_number`, `expiry_date`, `cvv`, `created_at`) VALUES
(1, 3, 'MG7uNwm8VVXVmYNYQ0i8uTzXJ9tTtx88kkNd6yQ6H05FMqOScvBj0ZI6', 'kCmEb0CU4MeTJqgFXsX06THIdAHbwHCHL+L0vqOGR2rYet2hTuQiy5n26g==', '28ogTcs0U67OIHScxH8CnlrQmbceL3Fiih+MPigI6CRQJv3UkST9JdbCo2M=', 'uXQ09HIlu1IPvLi25Jl6RMXJmhhX1ynDswe+lWK/ows=', 'EM2WOyEszkCFrARunuDmr+oTCLN9z1+hCN6PiA/ydA==', '2025-08-25 00:51:06'),
(6, 3, 'QTE0ghrIejGfn/5q+jTKO/dLrYqtHLsDNu+7Woll0KWX5myqg8GM9uSxtVP9HexPX0cwsEhBOdNyTzc=', 'wb0K8HGr9823OB7w8S33ytW76anvc3A23CneG0/d+tt+hVoi9BKy0VbLecmYVJkyXd36UiyDywYjF/E=', '+gYAKLdo6qGKjTV0KGALdKAJ9g5m5wukjZ5FBe62OBaU7HOAbzpTV4O6MUw=', 'LJTtwODFWlY8mzFlja4YdA5JQXxk8VGmcEjEQljgmX3O', 'aEvFk7umT8NfvO/ikgEQ24taOTg3kxIki6H0rAe2Gw==', '2025-09-02 06:10:03'),
(7, 5, '0f6GGcpsTCv2PnQ4cWqDGElYAQsWNi2IUJ8yAvE+UbO+rA==', 'tkO/tH6WboGaIji6bs/Xg032Y4HJg82eB8IZoC5xLimH', 'aBtNf6782GgSSebzn9MuvQFEYJm6V3KWiC5Ozpyo9Xz1ztMDFb8GkdeP1aRyg0I=', 'L9lLnnZbMhW3DUvX6SRHUnYn3onEJvErOGp7Mcr+c6cj', 'E7bJbi8C6ITi6G9H6iKSluaKSoVcdTfev7pTTxLvJw==', '2025-09-02 07:30:03'),
(8, 5, 'QlNXrAxXjjbPPNiFzXBzQcjou9p+14JWs2iX6P5zdyZXUOA=', 'R6RjwnFX+F/n5O2CvC1j4Bya25LhfDiky/Vdz4yGaB7T', 'YWU3FUc7QHCXOycUpCcDDPKnW97zm6+6UFFT3mhde/Ru209K4ZrUv5KzfflgxV8=', 'AXREAGF/elw4u9FaedHYk9MJho5brdcJHPLsVOarFeyY', 'IgNg3OQZYJumYt92lYZ+UkQvG5HBBJdd1ug+xuyOcA==', '2025-09-02 07:30:35');

-- --------------------------------------------------------

--
-- Estrutura para tabela `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `groups`
--

INSERT INTO `groups` (`id`, `user_id`, `name`, `created_at`) VALUES
(1, 3, 'Google', '2025-08-23 22:03:23'),
(2, 3, 'Spotify', '2025-08-25 00:46:32'),
(5, 3, 'iCloud', '2025-09-01 15:54:54'),
(6, 3, 'Senhas', '2025-09-01 15:55:11');

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `log_level` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `passwords`
--

CREATE TABLE `passwords` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `site_url` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `password` text NOT NULL,
  `recovery_codes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `passwords`
--

INSERT INTO `passwords` (`id`, `user_id`, `group_id`, `name`, `description`, `site_url`, `email`, `password`, `recovery_codes`, `created_at`) VALUES
(2, 3, 1, '9RTJYuiQWDcUfx0KHP02Zqw0JnuANllMJUa3bdOafM1As7h+ngoDZiLqRw==', 'wRgrvjKbHYZHXgZaeH23uuZFy/ZrHlEnoWonUgzacnot6xBFHJfsfJPYDFfO2VIrGJlXkF06IAGVyIfx/n8=', '1UZU6L28t8tTN6jvzwe8rA2lsc3fTapB+Ix+Ock8eGUiJf0181xbCdzLUskKulvDQhke', 'o/eE6enHjYsSMpAExCm4uEixVkk/yn4Wwzri29WgY3Z8RKaODnb0f9O2yL4/1KEhZuIzkENEtvQ=', 'IXEkUfafVwjWYwCxIG5kirJ2R1h+C4/iad6dkZNB3NfKB0m68suS', 'dsbRHPeh1cU4/VjkFaAkEJK/sSKOIxAA6RNMPzGAsbkJfpThdVY9fy3minsz7DCTbSiOKVpqi4vh3g6zgEknpvNYE52EqO5CE/J6Rw7ZQKq2h56xFF6iNYytyfRXm+mfKLETHKUJXGcwYnDbMw4eU9EILpQEqrTPURJBI7KUfzJjh60NyqZzSZZB+CfZswojAdqM7OJ7F7Taj69LkExkw6lZNFcAamTx0FIS', '2025-08-23 22:03:23'),
(4, 3, 2, 'et/YDqYJAvsnbpj9AGJV+OUkaoTToX590usiBWPqNoSt45o=', 'yzp3qBGmK2fSBVJtwHNFnxzQdSb7QI6Dgz0yqbQj28f1M9Htp93Kw7uvC4lckUxXX15MuCg=', 'SpFaGV4eDz0tJXD0skU0jZJnCSAEUDIFMMpxRphy/7eom7T/0N/EMuSJ8vDw1HbsQWJdh10=', 'bWAsG6lX4Ok49s1pzfzTd7eVjHj3ozEqddf12K+1C5rJUzAh1csuJwndxOBdIvs3+X8AEHQ68A==', 't8dY0FZnawcL74cEnDkRXb8MFufyVGg95jEj/hGj6LEUS49F7kPo', 'zKljBxAp456Dy+SuvkFvrmpzFdbzrEIfI27wMHxa', '2025-08-25 00:46:32'),
(5, 3, 2, 'nPRUiSqgWIQIPpewKKXf1YrrOk2W9Z4UhgTBWAjJ9c7DMrNSRiWxtWMkaQ==', 'TgLYUkcZlZho6UYYCx9lLGHicFs63sjCklb6TPzjHEg0mBw9XJd07deKNw==', 'dAH9eSNjHSMO0h9fCzC44roIecanN+gxz1xvVSvxKS4i8ENzNZfq+XOVhBpV/mhx898pfrA=', '2okWlK6Db3ztzW6yCNYbk7W4p0grG5XC01cZIH4W4Q89pTNJ/SQPCVN9dsSADrW/JRXpC6wIYcE=', 'bp4Lo1TtmTrvGChvZ8DffiYEp4X5OMAHtt62QHRftl0ZCEb3yS8Y', 'l5oKVd2eFwUcCkG+79bFtrR2zwMC2oLfCraWuQ9F', '2025-08-25 00:47:11'),
(7, 3, 1, '3xXLaZWfvbVTaqtM4h/IuZccWJQ5RS5e+JiB72o5P94ZG60H4psUIkNUKuhaUJ2z', '', 'J7NF/osAYFaGICkz0srD5ls+5EvdGd22xvKzxRNtkelyIP1Mj51e1ywPY4/NGVVWAbU8', '2RKcwCjc/QtxvEKg/pFBimIpltrHjFj5at0hh+mUlyuHK9wYkopQaY8bmdke3oTacz2XhTXI6g==', 'DxePSjvS0syhRKJMICNSq2HTsDoadY6ZRO+UhMR4mYPCmDYkP1i8Zosl4rjN/gw=', 'J30CoEyyZU8zENygxzpVH2ULuXR0pW8ejPNMd8WL', '2025-08-27 05:48:29'),
(8, 3, 6, 'EE500cX6V+7yHWavXATh9oDGFXNvBQYBKCnjcrZtZ40l', '', '7UtfNNpWP2vIuvTGp0E4A6p4CsqeSIn60/1yvRCpQzTCC39mJdUczRYfVDDbjnct75EGyijHhrR4rGVR74j28itPKpH1N2HAeUo=', 'D6F8YdTqv/CYsNczDibzSWeI0+/bTZBzeTzO16J2fOv8tw==', '2mKk6Dc2ERx31c6zOGDkf7Hw516KeS4GbXn3n+0vQA==', 'YGqs9B9w1ig0Bqqj0u/Vwpk9QhphO0DJBaLpLaL8', '2025-08-28 03:09:09'),
(9, 3, 1, 'VdcEnulBnAQyR36CgJtHU4mwcPqlF6U4ncCAl/xJCVMOyg==', '', 'z9p4pjcgRPpttiHxI6EyDq7GEri3q9yI9Cd6C1Zj5VF89wP0uS9/y+ccWd4mMTXC2BQx', 'unCBENQIUlizSyETzq1lO0W5chs3Jl/AVyCKluB1bhUT9FTwI3XzMTEUwzFFyWm6TIrQB6Jb', 'b/NnHUmpIsSFn1Np6p1jyLws2eXezN0B3W2gO+T6zTf23dRX', 'OtEAYlokhQi5NjS58JeRQe88PPwOoSPjamzkod9+', '2025-08-28 03:45:51'),
(10, 5, NULL, 'm4N+myqMo2/+a512g0jY2CWPtvr91yoRXziv6/9M+mtVuusQAf9qP3Kdbpo=', 'NVLiQnK6QVeYjSv7ivN68+lK/DQk0bIyqcgXjlcAg6FFmpgoMVw=', 'MZKGcsWDFG09mp0bWrTicIob1UnQQiNbBz147aAEmLev5tIbLDC95SDfQIVnpbk=', 't6XBw/bQhJcYi6TCzmNHm3jo3tfZ86wuQDygXbXR3V4jAYksiFCnjyo1', 'R8gWmPhlKEfGyJUUOBCNx7vanUhQxLUUtZkId7NXHtIWd+D1nw+A', 'Vng3kQ98jo3qhIw64V+A2YEjR/YxZwTx66lZIHWeF6/sDJvO6ycDQmVYuVblZbNLRsDSicmWdPg=', '2025-08-28 23:03:07'),
(11, 3, 6, 'MV/Y/7zL3H0RaCRPJDSzB4YbpyIUwY5RL8nCc+ZHVvsxsaq5RW4=', '', 'LfMID85m/HY6WsQQkkc1nECpIE8IqzXegGUNn+nL+0lHAR3/HmwwVZ800RbXEaVku9AFxPHAc+fDWNTVh4Du', '61tQcJrsrr4Oexw76J4BvKNUpqoVwzHI+qdImfAXvCr9XgsWHpErULhcew==', '6dGPa9Y2ECzbtUrxlSU49eT5zwrXb39RAUgVkoz8jQFUtECuXjjY', 'rnG7riv0YNCM0PA55ueJBx0l+AS628cXO905EcI6', '2025-09-01 04:51:28'),
(12, 3, 5, 'DBbkZCx7yVmWd/xyPOAXxlENMmALC13MPdR1aBlC2uUQ71lrlHJv8SkPaqw=', '', 'EZToD3+aRPseZcfmRjK0374B3xxWZF9d3tKIIr1GFvgKCBsJNuLPcOqO+rTBtEzzIBxM', 'x6Z+/RHAigDHwgNf58Y49RM1V6H6nzxg5IG/hUwLXzgiAEhX3OZqEQm2hLRgUFvz9RyVRYQrtQ==', 'Tr9DhFYnI/EcK+EFRVbTZ31wx8WqjttYiTNUUCKsXJxn5Qe/6WB1', 'SEJIp2rgV/ZGfCV+X8X9T74rD5fRyCUFmKvuSwzl', '2025-09-01 15:54:54'),
(13, 5, NULL, 'P33FcbG6n9lXjSWFAKoZCfs5qBxmhIwCvSFIQ2MbXfziUPg=', '', '4oTWeK2FljYaoh+8O8i4aun5u2toaBIc0icI9DlS3F+wBAnvkp7DbMp9zg09pqlQD8TjduI=', '96dF8sBEApUqIjyiz50haCu2nC35eWpdSy6DghyheQOoT6cbINayf1o6tGE=', 'oiMk8idvT0YGq0V15Yu9aJmpwk39PGya6qVtBIo7nU4OHXAm', 'bphCD0CS2EJYltCAn/H59xsczEeez9XcLx2yd19o', '2025-09-02 07:31:35'),
(14, 3, 6, 'ePKNqSqtpFrGaVAYXlYocT0T+VslEQf90/72TB7C1RTo/OU=', '', 'BC+6sUbFaSI9BYVYJRvoe1RL08VSW67XWsIwkcOsiy3rSdkMgM84ljjUG/5LjrEqOwn6Z/8=', 'BWI+AEKMQ49Lxstp4mOsrk4TY83X+s5aWUDuNBCJXjSMdL9IzhBEI53crSxl3Ghougrwmmq7', 'C4epQ96432ap3+PcAZBqGIba5E4NEsJBstPKYAnjHhz3+EGwbHBy', 'yTZWowl0cAicPbB5UtfxZ27s35bm/xrYcKs6b/Kx', '2025-09-02 07:45:10'),
(15, 3, NULL, 'dQC/8nOEQn3d2jsY+P5YD9NwNVBQ68wC70g6/L+JITcQEg==', '', 'EiFwvBN99MhRXlfhYBO+ixFnmL8NKCIAl2smKC3XgLU+2mNaxNPK9ocLs+JLMnI=', 'ZuHgGcYVez0HC1KZWTihFMlx6V8Rv1jT0KSLS7TnDOvelP58AKgjbZuT9+TBhshNt3ENRDKanw==', 'AdLQpGmL6lxRCo/ikOAnjAqrGt81b/pZvSNirlhqSV4RsV8JXnkc', '5VKo2n35ovjfE8/G8AdrWNqCnsRFrFnXUSY/LNBioVPT1pCUohAtC96ETzWoX30iLhHnLVwqMr+4OWBpee2P7ASiOTK38SRchKEbSpSoUcJYdWar8X+CrdZAxM2CCxA8ybnn4CXlcDdBFvowGoWrjPo1pbk3yLNndXAVNRDjAmnalhSVG3udpLuBOvI8mz4tMhs9gX1nBWrQd2cUivjD7hxOmscD9Ndo5gkBl0DlEv3Fn3/xqOkfgjfBwOjmCtkKPZ0ajuSzgRSdo/QSO/4rDxl6MO96t8gsZcAoip9Za9QTNwZw1tA=', '2025-09-24 20:36:36'),
(16, 3, NULL, 'gsvkFqHTV9Acy3FJ1G6s+U578RA+R+uKMt8hNz2AjcI=', '', '+KQRXkQDwhaO0aLaswp77Wm0d2b0kpGF/RJ4z0ax+CClApBZA5Wm9diZStAJ9ZxBgQ==', 'YpOoksgWO69PgRFoB5H+Wh/Cmm3/K2B+shzL9/tpP7pOMDxwq7rG6qO9qirMVfVAhjJEqiSCqg==', '0RD51/z6BgX3rZcycbyWdqY4XWnwMbvLSNH18Jo5ursvREMT5jNm9o8wpDQ=', 'dhl5y8xMn6n9CZG2oTiCvG1CTFP17qxesH1wfjCE', '2025-09-24 23:11:02');

-- --------------------------------------------------------

--
-- Estrutura para tabela `people`
--

CREATE TABLE `people` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `phone` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `people`
--

INSERT INTO `people` (`id`, `user_id`, `name`, `phone`, `created_at`) VALUES
(1, 3, 'NZGChRldJb5PKx96HAoUbozlz5O+KcvfBSr2XqAKOtg1fKHsyf6UL3TtWWs=', NULL, '2025-08-23 23:17:54'),
(2, 3, 'n1guQXSwZaKnzTVMv+vhh0fIpJNV3dqjlmTH3onc5thOVkvibkfi', NULL, '2025-08-25 01:12:25'),
(4, 3, 'dQz4crBH8y3lYRaAGviLGyUM22C+lbDRSzNFukDvDlmdZ/+VrWFz7rkR', NULL, '2025-08-25 01:12:36'),
(5, 3, 'u4nrc5348V47gomEbHRy4XhpfWLFTOMH9fgph/ek0KXvGRO7A3ASDiNp', NULL, '2025-08-25 01:12:40'),
(8, 3, 'LFptg9q11Ypg4+TJaJ9jYG7RxUWqnfc0ML41fClNYfIif7yb7q1m+KQ=', NULL, '2025-08-25 01:12:48'),
(9, 3, 'ksfAbXyNb2p0pNGG49MxrmlVFKTJq1zP2hD1k85izjpZBVuuR37/4jaqdnKu', NULL, '2025-08-25 01:12:56'),
(15, 3, 'Y5jl4b9a1FLGUwKGirqovwIOuqkKX7PHlC+JpMKyvyzVxG/BztgUiRDXdQ==', NULL, '2025-09-01 15:48:43'),
(16, 3, 'qLIpFtSHDyBYVaCgIsSH1Qz7BoAWLodVKxNeahzZw6H3FgRh9grs', NULL, '2025-09-01 15:48:54'),
(17, 3, '+5Cdj+e61YbMD4BDotnyXvfSZi2JPSBzCbKEZ4q9tyWZCdEo8qbDz6qewA==', NULL, '2025-09-01 15:49:17'),
(18, 3, 'd3Gsgsk0YZUmbPb6rnKF4sRk/JI8c08n/JiRX5z4fk7HnnUHXl6DDg==', NULL, '2025-09-01 15:49:45'),
(19, 3, 'rIlVX3YAUpfS9+jCekGCk43cxE554RP8jWfbiMOi3T+A9a6hkyhd', NULL, '2025-09-01 15:50:20'),
(20, 3, 'WEGcyc3tS2Ds8L44lBR1fNdeXEsjftG4fuyHvOKRFlYn5jRpt+sevQ==', NULL, '2025-09-01 15:50:43'),
(21, 3, 'Ed3FiNdfXi/P3WFp7vINXe9wErG7E1uLdREu4M26OmItgQ==', NULL, '2025-09-01 15:52:00'),
(22, 3, 'GBQSq+wREmye/6QwsKWgGLniiyRzgOGMDgaTKawqkK6rqGsyjw==', NULL, '2025-09-01 15:52:21'),
(23, 3, '+9AjZntbB5iPdMuD9rWugqYv2Qxpa6gdegBahtTTUt0/+EcDaSU=', NULL, '2025-09-01 15:52:41'),
(24, 3, 'uYd3deWeV0QbzM7cVwPvvo39s23ZCXlXqT+8OV6ugMPYOHOwvrMtvYot', NULL, '2025-09-01 15:52:55'),
(25, 3, 'FlzA28CKQdlvJ+HnDp8usMiCsMRLCAcmyoDJGRKnc+Ik7+rmAocTmgb2wQ==', NULL, '2025-09-01 15:53:16'),
(26, 5, 'w2hdf3P6tOz2vIkpjfnr887zQYKU39lXTj4KC1lcoHUd', NULL, '2025-09-02 07:32:00'),
(27, 5, 'CfXmA7JLJcFBEvRORAk81ui/FH0RCBM2Z9NvZIdNKWSCbw==', NULL, '2025-09-02 07:32:04'),
(28, 5, 'YlGLewbBVXFAt2dH1t+DZUl481/EF4Q+YQX6ObkpM+Q=', NULL, '2025-09-02 07:32:09');

-- --------------------------------------------------------

--
-- Estrutura para tabela `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `password_id` int(11) DEFAULT NULL,
  `card_id` int(11) DEFAULT NULL,
  `name` text NOT NULL,
  `value` text NOT NULL,
  `renewal_day` int(2) NOT NULL,
  `is_shared` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `user_id`, `password_id`, `card_id`, `name`, `value`, `renewal_day`, `is_shared`, `created_at`) VALUES
(5, 5, NULL, NULL, 'iCe24KsoWcNyhMr/q9qTqnAHQ00BkrR03WODGYe7xSxKV9dagH84uKb19Aw=', '6OQCl+9ReHRtc/HOdQEn6ISIBveG2M7RRc/fClpJ', 23, 1, '2025-08-28 22:44:36'),
(8, 3, 12, 1, 'lNIpvMXlE2Yj282d39OZF7nzZOSFgYFsDbe6o/y0n5WDSw==', 'l6soyFQrotcH3Y3V2hoMlzMwDJTv/9cLA+8iN/jSI1ye', 7, 1, '2025-09-02 07:26:18'),
(9, 5, 13, 7, 'CwgIFa4NYGw3SuQsX3mwYdZtqxPcsjCxITcCedRO39GLVQ8=', 'tZCLh9YczKsR2ZT5wMVTcTd1mBiTIlgrB4CE+JV/qVvb', 4, 1, '2025-09-02 07:32:42'),
(10, 3, 4, 1, 'rEvx6yc5CDD4ZOHfJsURLMa4ozCizxSRJ1wbcQjBawl4Urc=', 'zN2YHIbT/nXFxk7vW5kMMuya4ba5dEGAVvgGzca0', 7, 1, '2025-09-02 14:57:40'),
(12, 3, 5, 1, 'fOWXb3OoMz2SYbtctR2/uhg8lTAgIiYqOaYYpfB1Q7Sh9Y/E6A==', 'tPAk6kOY7Ja+DWxl9JS7N5fx1RgOW9XLMXHTyv9N', 7, 1, '2025-09-02 14:59:53');

-- --------------------------------------------------------

--
-- Estrutura para tabela `subscription_payments`
--

CREATE TABLE `subscription_payments` (
  `subscription_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `payment_month` int(2) NOT NULL,
  `payment_year` int(4) NOT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `subscription_payments`
--

INSERT INTO `subscription_payments` (`subscription_id`, `person_id`, `payment_month`, `payment_year`, `paid_at`) VALUES
(8, 1, 9, 2025, '2025-09-09 01:39:06'),
(8, 1, 10, 2025, '2025-10-03 14:00:31'),
(8, 5, 9, 2025, '2025-09-09 01:39:06'),
(8, 22, 9, 2025, '2025-09-09 01:39:06'),
(8, 23, 9, 2025, '2025-09-09 01:39:06'),
(8, 24, 9, 2025, '2025-09-09 01:39:06'),
(8, 25, 9, 2025, '2025-09-09 01:39:06'),
(10, 2, 9, 2025, '2025-09-15 09:08:05'),
(10, 5, 9, 2025, '2025-09-15 09:08:05'),
(10, 9, 1, 2026, '2025-09-05 01:03:52'),
(10, 9, 2, 2026, '2025-09-05 01:03:52'),
(10, 9, 3, 2026, '2025-09-05 01:03:52'),
(10, 9, 4, 2026, '2025-09-05 01:03:52'),
(10, 9, 5, 2026, '2025-09-05 01:03:52'),
(10, 9, 6, 2026, '2025-09-05 01:03:52'),
(10, 9, 9, 2025, '2025-09-15 09:08:05'),
(10, 9, 10, 2025, '2025-09-05 01:03:52'),
(10, 9, 11, 2025, '2025-09-05 01:03:52'),
(10, 9, 12, 2025, '2025-09-05 01:03:52'),
(10, 19, 9, 2025, '2025-09-15 09:08:05'),
(10, 20, 9, 2025, '2025-09-15 09:08:05'),
(10, 21, 9, 2025, '2025-09-15 09:08:05'),
(12, 4, 9, 2025, '2025-09-09 01:39:00'),
(12, 8, 9, 2025, '2025-09-09 01:39:00'),
(12, 15, 9, 2025, '2025-09-09 01:39:00'),
(12, 16, 9, 2025, '2025-09-09 01:39:00'),
(12, 17, 9, 2025, '2025-09-09 01:39:00'),
(12, 18, 9, 2025, '2025-09-09 01:39:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `subscription_people`
--

CREATE TABLE `subscription_people` (
  `subscription_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `subscription_people`
--

INSERT INTO `subscription_people` (`subscription_id`, `person_id`) VALUES
(8, 1),
(10, 2),
(12, 4),
(8, 5),
(10, 5),
(12, 8),
(10, 9),
(12, 15),
(12, 16),
(12, 17),
(12, 18),
(10, 19),
(10, 20),
(10, 21),
(8, 22),
(8, 23),
(8, 24),
(8, 25),
(9, 26),
(9, 27);

-- --------------------------------------------------------

--
-- Estrutura para tabela `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` text NOT NULL,
  `description` text DEFAULT NULL,
  `value` varchar(512) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `transaction_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `transaction_categories`
--

CREATE TABLE `transaction_categories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Despejando dados para a tabela `transaction_categories`
--

INSERT INTO `transaction_categories` (`id`, `user_id`, `name`) VALUES
(1, 3, 'Lazer'),
(2, 3, 'Veículo'),
(3, 3, 'Contas'),
(4, 3, 'Mercado');

-- --------------------------------------------------------

--
-- Estrutura para tabela `two_factor_auth`
--

CREATE TABLE `two_factor_auth` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `password_id` int(11) NOT NULL,
  `secret_key` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `two_factor_auth`
--

INSERT INTO `two_factor_auth` (`id`, `user_id`, `password_id`, `secret_key`, `created_at`) VALUES
(4, 3, 9, 'KubHGuDvKwccQflUPMrXPTIevB6Ql1b+z+zDvYbNjIWc657NwcVkeX3TlPGwxb5jxDxyEdI7bQz8HCrH', '2025-09-02 06:59:00'),
(5, 5, 13, 'mptW472Spjq1mnUHxbnQ64pNuo0MWb02WIEAJWw/+7VUCxQfsIntE8uN70EsLcmlLacfsdjHhTnaQRO7', '2025-09-02 07:31:45'),
(6, 3, 15, '/cvDuaNrO97GM/i0h9XgbAKJNhSovSg+u/zyqAXqYLdzLwDeGT1U1acH5MY=', '2025-09-24 20:36:44');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `vault_password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `vault_password_hash`, `created_at`) VALUES
(3, 'lucena', '$2y$10$RI5atBBZeT8YRQQQbK7SI.U0s6Oy7P9hNIUoYw.af5Ll9spYVUXua', '$2y$10$iz5.7t/eZpjQ6jY9bH5cGObncs.b1WMONGA3Y4XAHhU2YKAtobDKq', '2025-08-23 21:21:19'),
(4, 'a', '$2y$10$.9J2gcBDcsimzjLdz2LSWuupvYlZlmiAL.dAWIpG61O/1mWmpN2XO', '$2y$10$4RnkdwV1dWFemmeBQClAFeyTQiDwlAHHCoKSeoLdDcE02IKzsGaYS', '2025-08-25 03:12:14'),
(5, 'nygel', '$2y$10$6KbMiuCvjoI3STX.lH9r.uLNRYWXVA3rz2tISIo1x6tuMPzXfiXh2', '$2y$10$y3RBQPxGMlnw6c7854oVVOVPsSrOyGyqs0B3P6GSesPmkCKJcccia', '2025-08-28 20:59:09');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `passwords`
--
ALTER TABLE `passwords`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Índices de tabela `people`
--
ALTER TABLE `people`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `password_id` (`password_id`),
  ADD KEY `card_id` (`card_id`);

--
-- Índices de tabela `subscription_payments`
--
ALTER TABLE `subscription_payments`
  ADD PRIMARY KEY (`subscription_id`,`person_id`,`payment_month`,`payment_year`),
  ADD KEY `person_id` (`person_id`);

--
-- Índices de tabela `subscription_people`
--
ALTER TABLE `subscription_people`
  ADD PRIMARY KEY (`subscription_id`,`person_id`),
  ADD KEY `person_id` (`person_id`);

--
-- Índices de tabela `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Índices de tabela `transaction_categories`
--
ALTER TABLE `transaction_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `two_factor_auth`
--
ALTER TABLE `two_factor_auth`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `password_id` (`password_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `bills`
--
ALTER TABLE `bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de tabela `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `passwords`
--
ALTER TABLE `passwords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `people`
--
ALTER TABLE `people`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de tabela `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `transaction_categories`
--
ALTER TABLE `transaction_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `two_factor_auth`
--
ALTER TABLE `two_factor_auth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `cards`
--
ALTER TABLE `cards`
  ADD CONSTRAINT `cards_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `passwords`
--
ALTER TABLE `passwords`
  ADD CONSTRAINT `passwords_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `passwords_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `people`
--
ALTER TABLE `people`
  ADD CONSTRAINT `people_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscriptions_ibfk_2` FOREIGN KEY (`password_id`) REFERENCES `passwords` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `subscriptions_ibfk_3` FOREIGN KEY (`card_id`) REFERENCES `cards` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `subscription_payments`
--
ALTER TABLE `subscription_payments`
  ADD CONSTRAINT `subscription_payments_ibfk_1` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscription_payments_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `subscription_people`
--
ALTER TABLE `subscription_people`
  ADD CONSTRAINT `subscription_people_ibfk_1` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscription_people_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `people` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `two_factor_auth`
--
ALTER TABLE `two_factor_auth`
  ADD CONSTRAINT `two_factor_auth_ibfk_1` FOREIGN KEY (`password_id`) REFERENCES `passwords` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
