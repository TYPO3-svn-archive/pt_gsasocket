#
# Table structure for table `px_DTABUCH` (as at Paradox database file "DTABUCH.DB" of v2.8.11.1)
#

CREATE TABLE px_DTABUCH (
    NUMMER INTEGER auto_increment,
    NAME VARCHAR(27),
    NAME2 VARCHAR(27),
    BLZ VARCHAR(8),
    KONTO VARCHAR(10),
    TYP VARCHAR(20),
    BETRAG DECIMAL(12,2),
    ZWECK VARCHAR(27),
    ZWECK2 VARCHAR(27),
    PROGRAMM VARCHAR(10),
    DATUM DATE,
    FAELLIG DATE,
    BUCHDAT DATE,
    DISKID VARCHAR(3),
    MEHRZWECK BLOB,
    EURO INTEGER(1),
    PRIMARY KEY (NUMMER)
);

