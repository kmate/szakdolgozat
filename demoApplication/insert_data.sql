SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;

INSERT INTO `task` (`id`, `user_id`, `title`, `description`, `start`, `finish`, `priority`, `is_public`) VALUES
(1, 1, 'Szakdolgozat leadása', 'Tanszéki titkárságon, 16:00-ig', '2011-05-16', '2011-05-16', 'high', 1),
(2, 1, 'Vizsga', 'Numerikus módszerek II.', '2011-05-30', '2011-05-30', 'normal', 0),
(3, 1, 'Záróvizsga', '', '2011-06-27', '2011-06-29', 'high', 1),
(4, 1, 'MSc felvételi', '', '2011-07-04', '2011-07-06', 'normal', 1),
(5, 2, 'Mozi', '&#34;Thor&#34;,&#13;&#10;19:00-21:00,&#13;&#10;MOM Palace Cinemas', '2011-05-22', '2011-05-22', 'low', 1),
(6, 2, 'ZH', 'Logika és számításelmélet,&#13;&#10;08:00, D0-114', '2011-05-16', '2011-05-16', 'normal', 0),
(7, 2, 'Nyári edzőtábor', 'Képzőművészeti Egyetem Művésztelep, 8237, Tihany. Major u.64.', '2011-07-18', '2011-07-24', 'normal', 1),
(8, 2, 'Szakdolgozat köttetése', '', '2011-05-13', '2011-05-13', 'high', 0),
(9, 1, 'Utolsó ZH', 'D0-114, 08:00', '2011-05-16', '2011-05-16', 'normal', 1),
(10, 1, 'Tavaszi szünet', '', '2011-04-18', '2011-04-26', 'low', 1),
(11, 2, 'Bemutató program tesztelése', '', '2011-05-02', '2011-05-13', 'normal', 1),
(12, 2, 'Hegyalja fesztivál', 'Tokaj-Rakamaz', '2011-07-13', '2011-07-17', 'low', 1),
(13, 2, 'Pannónia fesztivál', 'Várpalota', '2011-06-09', '2011-06-13', 'normal', 1),
(14, 3, 'Tesztelés', 'Publikus feladat hozzáadása', '2011-04-25', '2011-05-12', 'normal', 1),
(15, 3, 'Születésnap szervezése', '', '2011-08-03', '2011-08-03', 'high', 0),
(16, 1, 'Nyaralás', 'Balaton - Siófok', '2011-07-30', '2011-08-07', 'normal', 1);

INSERT INTO `user` (`id`, `name`, `full_name`, `email`, `password`) VALUES
(1, 'kampaai', 'Karácsony Máté', 'k_mate@inf.elte.hu', '116d359b19a85e5b9e382d87782521a311381e0c51fd83b932'),
(2, 'gipszj', 'Gipsz Jakab', 'jakab@gipsz.hu', 'd6862c1491c73413e05af97ad7cad302ca728eb751b6c4976e'),
(3, 'kovacs', 'Kovács János', 'kovacs.janos@mail.hu', 'e65f70e280e034992cb3eafbc0ffdbef7c7611b3a975293a33');
SET FOREIGN_KEY_CHECKS=1;
COMMIT;