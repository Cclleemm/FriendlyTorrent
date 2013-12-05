-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le : Mer 04 Décembre 2013 à 17:57
-- Version du serveur: 5.5.32
-- Version de PHP: 5.3.10-1ubuntu3.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `board`
--

-- --------------------------------------------------------

--
-- Structure de la table `cacheRss`
--

CREATE TABLE IF NOT EXISTS `cacheRss` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(250) NOT NULL,
  `nameFile` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2408 ;

-- --------------------------------------------------------

--
-- Structure de la table `checkDownload`
--

CREATE TABLE IF NOT EXISTS `checkDownload` (
  `idFile` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `time` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `id` varchar(50) NOT NULL,
  `valeur` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `downloads`
--

CREATE TABLE IF NOT EXISTS `downloads` (
  `clef` varchar(32) NOT NULL,
  `idFichier` int(11) NOT NULL,
  `linkFile` varchar(1000) NOT NULL,
  PRIMARY KEY (`clef`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `etapes`
--

CREATE TABLE IF NOT EXISTS `etapes` (
  `id` varchar(50) NOT NULL,
  `echeance` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` varchar(767) NOT NULL,
  `type` varchar(10) NOT NULL,
  `idBoxe` int(11) NOT NULL,
  `taille` int(11) NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `link` (`link`,`type`,`idBoxe`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=124271 ;

-- --------------------------------------------------------

--
-- Structure de la table `lastSeen`
--

CREATE TABLE IF NOT EXISTS `lastSeen` (
  `idUser` int(11) NOT NULL,
  `idCloud` int(11) NOT NULL,
  `time` bigint(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `messagerie`
--

CREATE TABLE IF NOT EXISTS `messagerie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idUser` int(11) NOT NULL,
  `idUserTarget` int(11) NOT NULL,
  `text` text NOT NULL,
  `seen` int(11) NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=840 ;

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

CREATE TABLE IF NOT EXISTS `paiements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idUser` int(11) NOT NULL,
  `nbrJours` int(11) NOT NULL,
  `price` float(50,2) NOT NULL,
  `time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `cle` varchar(50) NOT NULL,
  `idUser` int(11) NOT NULL,
  `time` bigint(20) NOT NULL,
  `lastTime` bigint(20) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `user_agent` text NOT NULL,
  `ipLocalisation` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `torrents`
--

CREATE TABLE IF NOT EXISTS `torrents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idBoxe` int(11) NOT NULL,
  `time` bigint(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  `datapath` varchar(150) NOT NULL,
  `hash` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idBoxe` (`idBoxe`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6748 ;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `boxe` varchar(100) NOT NULL,
  `couleur` varchar(11) NOT NULL,
  `lastScan` bigint(20) NOT NULL,
  `rss` varchar(300) NOT NULL,
  `admin` int(11) NOT NULL,
  `port` int(11) NOT NULL,
  `nbrJours` int(11) NOT NULL,
  `bulleAbo` int(11) NOT NULL,
  `bulleSpace` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Structure de la table `xferTorrent`
--

CREATE TABLE IF NOT EXISTS `xferTorrent` (
  `idTorrent` int(11) NOT NULL,
  `lastUp` bigint(20) unsigned NOT NULL,
  `lastDown` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`idTorrent`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `xferUser`
--

CREATE TABLE IF NOT EXISTS `xferUser` (
  `idUser` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `month` int(11) NOT NULL,
  `totalUp` bigint(11) unsigned NOT NULL,
  `totalDown` bigint(11) unsigned NOT NULL,
  PRIMARY KEY (`idUser`,`year`,`month`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
