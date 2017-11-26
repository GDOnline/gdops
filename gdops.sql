SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `opsacccommentbans` (
  `accountID` int(11) NOT NULL DEFAULT '0',
  `reason` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsaccountcomments` (
  `commentID` int(11) NOT NULL,
  `accountID` int(11) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `likes` int(11) NOT NULL DEFAULT '0',
  `isSpam` int(11) NOT NULL DEFAULT '0',
  `uploadTime` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsaccountprofiles` (
  `accountID` int(11) NOT NULL,
  `youtube` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `twitch` varchar(255) DEFAULT NULL,
  `allowFriendRequests` int(11) NOT NULL DEFAULT '0',
  `allowMessages` int(11) NOT NULL DEFAULT '0',
  `allowCommentHistory` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsaccounts` (
  `accountID` int(11) NOT NULL,
  `userName` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `actCode` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsbannedusers` (
  `userID` int(11) NOT NULL,
  `reason` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsblockedusers` (
  `accountID` int(11) NOT NULL,
  `targetAccountID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opscommentbans` (
  `accountID` int(11) NOT NULL,
  `reason` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opscomments` (
  `commentID` int(11) NOT NULL,
  `userID` int(11) NOT NULL DEFAULT '0',
  `likes` int(11) NOT NULL DEFAULT '0',
  `comment` varchar(255) DEFAULT NULL,
  `percent` int(11) NOT NULL DEFAULT '0',
  `uploadTime` int(11) NOT NULL DEFAULT '0',
  `levelID` int(11) NOT NULL DEFAULT '0',
  `isSpam` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsdailylevels` (
  `dailyID` int(11) NOT NULL,
  `levelID` int(11) NOT NULL DEFAULT '0',
  `isWeekly` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsdisabledaccounts` (
  `accountID` int(11) NOT NULL,
  `reason` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsfriendrequests` (
  `requestID` int(11) NOT NULL,
  `accountID` int(11) NOT NULL DEFAULT '0',
  `targetAccountID` int(11) NOT NULL DEFAULT '0',
  `comment` text,
  `isNew` int(11) NOT NULL DEFAULT '1',
  `uploadTime` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsfriends` (
  `fsID` int(11) NOT NULL,
  `accountID` int(11) NOT NULL DEFAULT '0',
  `targetAccountID` int(11) NOT NULL DEFAULT '0',
  `isNew` int(11) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsgauntlets` (
  `gauntletID` int(11) NOT NULL,
  `levels` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsipdownloaded` (
  `IP` varchar(255) DEFAULT NULL,
  `levelID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsipliked` (
  `IP` varchar(255) DEFAULT NULL,
  `itemID` int(11) NOT NULL,
  `udid` varchar(255) DEFAULT NULL,
  `itemType` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsiplimits` (
  `IP` text,
  `udid` text,
  `messages` int(11) NOT NULL DEFAULT '0',
  `userID` int(11) NOT NULL DEFAULT '0',
  `accountID` int(11) NOT NULL DEFAULT '0',
  `comments` int(11) NOT NULL DEFAULT '0',
  `accountComments` int(11) NOT NULL DEFAULT '0',
  `likes` int(11) NOT NULL DEFAULT '0',
  `levels` int(11) NOT NULL DEFAULT '0',
  `rates` int(11) NOT NULL DEFAULT '0',
  `mr` int(11) NOT NULL DEFAULT '0',
  `cr` int(11) NOT NULL DEFAULT '0',
  `ar` int(11) NOT NULL DEFAULT '0',
  `lr` int(11) NOT NULL DEFAULT '0',
  `ler` int(11) NOT NULL DEFAULT '0',
  `rr` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsiprated` (
  `IP` text,
  `udid` text,
  `levelID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opslevelrates` (
  `levelID` int(11) NOT NULL,
  `totalRates` int(11) NOT NULL DEFAULT '0',
  `totalStars` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opslevelreports` (
  `levelID` int(11) NOT NULL,
  `reportCount` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opslevelrequests` (
  `levelID` int(11) NOT NULL,
  `accountID` int(11) NOT NULL,
  `stars` int(11) NOT NULL DEFAULT '0',
  `isFeatured` int(11) NOT NULL DEFAULT '0',
  `isDemon` int(11) NOT NULL DEFAULT '0',
  `demonType` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opslevels` (
  `levelID` int(11) NOT NULL,
  `levelName` varchar(255) DEFAULT NULL,
  `levelDesc` varchar(255) DEFAULT NULL,
  `levelVersion` int(11) NOT NULL DEFAULT '1',
  `userID` int(11) NOT NULL DEFAULT '0',
  `levelDifficulty` int(11) NOT NULL DEFAULT '0',
  `downloads` int(11) NOT NULL DEFAULT '0',
  `song` int(11) NOT NULL DEFAULT '0',
  `gameVersion` int(11) NOT NULL DEFAULT '0',
  `likes` int(11) NOT NULL DEFAULT '0',
  `isDemon` int(11) NOT NULL DEFAULT '0',
  `demonType` int(11) NOT NULL DEFAULT '0',
  `isAuto` int(11) NOT NULL DEFAULT '0',
  `stars` int(11) NOT NULL DEFAULT '0',
  `isFeatured` int(11) NOT NULL DEFAULT '0',
  `isEpic` int(11) NOT NULL DEFAULT '0',
  `levelLength` int(11) NOT NULL DEFAULT '0',
  `originalID` int(11) NOT NULL DEFAULT '0',
  `uploadTime` int(11) NOT NULL DEFAULT '0',
  `updateTime` int(11) NOT NULL DEFAULT '0',
  `customSongID` int(11) NOT NULL DEFAULT '0',
  `extraString` text,
  `coins` int(11) NOT NULL DEFAULT '0',
  `isVerified` int(11) NOT NULL DEFAULT '0',
  `requestedStars` int(11) NOT NULL DEFAULT '0',
  `password` int(11) NOT NULL DEFAULT '0',
  `isTwoPlayer` int(11) NOT NULL DEFAULT '0',
  `isFame` int(11) NOT NULL DEFAULT '0',
  `isUnlisted` int(11) NOT NULL DEFAULT '0',
  `objects` int(11) NOT NULL DEFAULT '0',
  `ldm` int(11) NOT NULL DEFAULT '0',
  `levelInfo` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsmappacks` (
  `packID` int(11) NOT NULL,
  `packName` text,
  `packLevels` text,
  `packStars` int(11) NOT NULL DEFAULT '0',
  `packCoins` int(11) NOT NULL DEFAULT '0',
  `packDifficulty` int(11) NOT NULL DEFAULT '0',
  `packColor` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsmessages` (
  `messageID` int(11) NOT NULL,
  `accountID` int(11) NOT NULL DEFAULT '0',
  `targetAccountID` int(11) NOT NULL DEFAULT '0',
  `isRead` int(11) NOT NULL DEFAULT '0',
  `uploadTime` int(11) NOT NULL DEFAULT '0',
  `subject` text,
  `body` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsmoderators` (
  `modID` int(11) NOT NULL,
  `accountID` int(11) NOT NULL,
  `modType` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opspercentages` (
  `levelID` int(11) NOT NULL DEFAULT '0',
  `accountID` int(11) NOT NULL DEFAULT '0',
  `percent` int(11) NOT NULL DEFAULT '0',
  `updateTime` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsreuploadedlevels` (
  `original` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opssongrequests` (
  `name` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `url` mediumtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opssongs` (
  `songID` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsuserchests` (
  `userID` int(11) NOT NULL DEFAULT '0',
  `bigChestTime` int(11) NOT NULL DEFAULT '0',
  `smallChestTime` int(11) NOT NULL DEFAULT '0',
  `bigChestOpened` int(11) NOT NULL DEFAULT '0',
  `smallChestOpened` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsuserrewards` (
  `userID` int(11) NOT NULL,
  `getTime` int(11) NOT NULL,
  `special` int(11) NOT NULL DEFAULT '0',
  `type` varchar(50) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsusers` (
  `userID` int(11) NOT NULL,
  `accountID` int(11) NOT NULL DEFAULT '0',
  `udid` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsuserscores` (
  `userID` int(11) NOT NULL DEFAULT '0',
  `stars` int(11) NOT NULL DEFAULT '0',
  `demons` int(11) NOT NULL DEFAULT '0',
  `coins` int(11) NOT NULL DEFAULT '0',
  `userCoins` int(11) NOT NULL DEFAULT '0',
  `special` int(11) NOT NULL DEFAULT '0',
  `accIcon` int(11) NOT NULL DEFAULT '0',
  `accShip` int(11) NOT NULL DEFAULT '0',
  `accBall` int(11) NOT NULL DEFAULT '0',
  `accBird` int(11) NOT NULL DEFAULT '0',
  `accDart` int(11) NOT NULL DEFAULT '0',
  `accRobot` int(11) NOT NULL DEFAULT '0',
  `accGlow` int(11) NOT NULL DEFAULT '0',
  `accSpider` int(11) NOT NULL DEFAULT '0',
  `accExplosion` int(11) NOT NULL DEFAULT '0',
  `diamonds` int(11) NOT NULL DEFAULT '0',
  `color1` int(11) NOT NULL DEFAULT '0',
  `color2` int(11) NOT NULL DEFAULT '0',
  `iconType` int(11) NOT NULL DEFAULT '0',
  `icon` int(11) NOT NULL DEFAULT '0',
  `userName` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `opsusertokens` (
  `accountID` int(11) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `createdAt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `opsacccommentbans`
  ADD PRIMARY KEY (`accountID`);

ALTER TABLE `opsaccountcomments`
  ADD PRIMARY KEY (`commentID`);

ALTER TABLE `opsaccountprofiles`
  ADD PRIMARY KEY (`accountID`);

ALTER TABLE `opsaccounts`
  ADD PRIMARY KEY (`accountID`);

ALTER TABLE `opsbannedusers`
  ADD PRIMARY KEY (`userID`);

ALTER TABLE `opscommentbans`
  ADD PRIMARY KEY (`accountID`);

ALTER TABLE `opscomments`
  ADD PRIMARY KEY (`commentID`);

ALTER TABLE `opsdailylevels`
  ADD PRIMARY KEY (`dailyID`);

ALTER TABLE `opsdisabledaccounts`
  ADD PRIMARY KEY (`accountID`);

ALTER TABLE `opsfriendrequests`
  ADD PRIMARY KEY (`requestID`);

ALTER TABLE `opsfriends`
  ADD PRIMARY KEY (`fsID`);

ALTER TABLE `opsgauntlets`
  ADD PRIMARY KEY (`gauntletID`);

ALTER TABLE `opslevels`
  ADD PRIMARY KEY (`levelID`);

ALTER TABLE `opsmappacks`
  ADD PRIMARY KEY (`packID`);

ALTER TABLE `opsmessages`
  ADD PRIMARY KEY (`messageID`);

ALTER TABLE `opsmoderators`
  ADD PRIMARY KEY (`modID`);

ALTER TABLE `opspercentages`
  ADD PRIMARY KEY (`levelID`);

ALTER TABLE `opsuserchests`
  ADD PRIMARY KEY (`userID`);

ALTER TABLE `opsusers`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `udid` (`udid`);

ALTER TABLE `opsuserscores`
  ADD PRIMARY KEY (`userID`);


ALTER TABLE `opsaccountcomments`
  MODIFY `commentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=628;

ALTER TABLE `opsaccounts`
  MODIFY `accountID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=900;

ALTER TABLE `opscomments`
  MODIFY `commentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2518;

ALTER TABLE `opsdailylevels`
  MODIFY `dailyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `opsfriendrequests`
  MODIFY `requestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1153;

ALTER TABLE `opsfriends`
  MODIFY `fsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1255;

ALTER TABLE `opslevels`
  MODIFY `levelID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=761;

ALTER TABLE `opsmappacks`
  MODIFY `packID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `opsmessages`
  MODIFY `messageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=560;

ALTER TABLE `opsmoderators`
  MODIFY `modID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

ALTER TABLE `opsusers`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1453;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
