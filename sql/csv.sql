--DBName internaldlv

create table `csv_fileName`(
	`id` int(10) unsigned auto_increment primary key,
	`fileNumber` int(3) unsigned not null,
	`modified` int(11) not null default '0' comment 'timestamp for when fileNumber changed',
	`created` int(11) not null default '0' comment 'timestamp for when fileNumber created' 
	
)engine = MyISAM default charset=utf8;

create table `csv_reference`(
	`id` int(10) unsigned auto_increment primary key,
	`reference` int(4) unsigned not null,
	`modified` int(11) not null default '0' comment 'timestamp for when refference changed',
	`created` int(11) not null default '0' comment 'timestamp for when refference created' 
)engine = MyISAM default charset=utf8;
