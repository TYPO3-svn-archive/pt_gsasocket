#
# Structure of table `px_laender`
#

DROP TABLE IF EXISTS `px_laender`;
CREATE TABLE `px_laender` (
  `NUMMER` int(11) default NULL,
  `KUERZEL` char(3) default NULL,
  `NAME` varchar(35) default NULL,
  `WEBLKZ` char(2) default NULL,
  `VWREIN` varchar(4) default NULL,
  `VWRAUS` varchar(4) default NULL
) TYPE=MyISAM;

#
# Data for table `px_laender` (as needed for pt_gsauserreg v0.0.10 and up)
#

INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (0, 'D', 'Deutschland', 'de', '49', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (1, 'CH', 'Schweiz', 'ch', '41', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (2, 'B', 'Belgien', 'be', '32', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (3, 'DK', 'Dänemark', 'dk', '45', '009');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (4, 'FIN', 'Finnland', 'fi', '358', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (5, 'F', 'Frankreich', 'fr', '33', '19');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (6, 'GR', 'Griechenland', 'gr', '30', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (7, 'GB', 'Großbritannien', 'uk', '44', '010');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (8, 'IRL', 'Irland', 'ie', '353', '16');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (9, 'IL', 'Israel', 'il', '972', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (10, 'I', 'Italien', 'it', '39', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (11, 'NL', 'Niederlande', 'nl', '31', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (12, 'N', 'Norwegen', 'no', '47', '095');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (13, 'P', 'Portugal', 'pt', '351', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (14, 'S', 'Schweden', 'se', '46', '009');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (15, 'E', 'Spanien', 'es', '34', '07');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (16, 'TR', 'Türkei', 'tr', '90', '99');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (17, 'HU', 'Ungarn', 'hu', '36', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (18, 'USA', 'Usa', 'us', '1', '011');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (19, 'CDN', 'Kanada', 'ca', '1', '011');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (20, 'L', 'Luxemburg', 'lu', '35', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (21, 'A', 'Österreich', 'at', '43', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (22, 'FL', 'Liechtenstein', 'li', '41', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (23, 'CZ', 'Tschechien', 'cz', '42', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (24, 'PL', 'Polen', 'pl', '48', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (25, 'AL', 'Albanien', 'al', '355', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (26, 'AND', 'Andorra', 'an', '367', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (27, 'ARM', 'Armenien', 'am', '7', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (28, 'AUS', 'Australien', 'au', '61', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (29, 'BG', 'Bulgarien', 'bg', '359', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (30, 'BIH', 'Bosnien Herzegowina', 'bi', '387', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (31, 'BY', 'Weißrussland', 'by', '7', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (32, 'CY', 'Zypern', 'cy', '357', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (33, 'DZ', 'Algerien', 'dz', '213', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (34, 'EST', 'Estland', 'ee', '372', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (35, 'FJI', 'Fidschi', 'fj', '679', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (36, 'GBZ', 'Gibraltar', 'gi', '350', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (37, 'GE', 'Georgien', 'ge', '995', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (38, 'HR', 'Kroatien', 'hr', '385', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (39, 'IS', 'Island', 'is', '354', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (40, 'J', 'Japan', 'jp', '81', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (41, 'JOR', 'Jordanien', 'jo', '962', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (42, 'KS', 'Kirgistan', 'ks', '7', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (43, 'KZ', 'Kasachstan', 'kz', '7', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (44, 'LT', 'Litauen', 'lt', '370', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (45, 'LV', 'Lettland', 'lv', '371', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (46, 'M', 'Malta', 'mt', '356', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (47, 'MA', 'Marokko', 'ma', '212', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (48, 'MC', 'Monaco', 'mc', '33', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (49, 'MD', 'Moldau', 'md', '691', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (50, 'NZ', 'Neuseeland', 'nz', '64', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (51, 'RC', 'Taiwan', 'tw', '886', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (52, 'RO', 'Rumänien', 'ro', '40', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (53, 'RUS', 'Russland', 'ru', '7', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (54, 'SK', 'Slowakei', 'sk', '42', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (55, 'SLO', 'Slowenien', 'si', '386', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (56, 'TJ', 'Tadschikistan', 'tj', '7', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (57, 'TN', 'Tunesien', 'tn', '216', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (58, 'TO', 'Tonga', 'to', '676', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (59, 'UA', 'Ukraine', 'ua', '70', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (60, 'UZB', 'Usbekistan', 'uz', '7', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (61, 'VN', 'Vietnam', 'vn', '84', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (62, 'YU', 'Jugoslawien', 'yu', '38', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (63, 'ZA', 'Südafrika', 'za', '27', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (64, 'VRC', 'Volksrepublik China', 'cn', '86', '');
INSERT INTO `px_laender` (`NUMMER`, `KUERZEL`, `NAME`, `WEBLKZ`, `VWREIN`, `VWRAUS`) VALUES (65, 'BR', 'Brasilien', 'br', '55', '');

