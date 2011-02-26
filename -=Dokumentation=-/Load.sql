-- TYPE
delete from type;
insert into type (type_id, type_name) values (1, 	"Passes");
insert into type (type_id, type_name) values (2, 	"Spins");
insert into type (type_id, type_name) values (3, 	"Bounces");
insert into type (type_id, type_name) values (4, 	"Stops");
insert into type (type_id, type_name) values (5, 	"Strikes");
insert into type (type_id, type_name) values (6, 	"Blocks");
insert into type (type_id, type_name) values (7, 	"Twirls");
insert into type (type_id, type_name) values (8, 	"Doubles");
insert into type (type_id, type_name) values (9, 	"Misc");
insert into type (type_id, type_name) values (10, "Combos");
insert into type (type_id, type_name) values (11, "Forms");
insert into type (type_id, type_name) values (12, "Freeforms");

-- difficulty
delete from difficulty;
insert into difficulty(difficulty_id, difficulty_name) values(1, "Grasshopper");
insert into difficulty(difficulty_id, difficulty_name) values(2, "Beginner");
insert into difficulty(difficulty_id, difficulty_name) values(3, "InterMediate");
insert into difficulty(difficulty_id, difficulty_name) values(4, "Master");
insert into difficulty(difficulty_id, difficulty_name) values(5, "Godlike");

-- user
delete from user;
insert into user (user_id, user_name) values(1, "Master Po");
insert into user (user_id, user_name) values(2, "Gigante");

-- links
delete from link;

insert into link(url, link_name, comment, name, email, verified) values ("http://soulgrind22.homestead.com/", "Soulgrind's website on Nunchaku", "lots of videos and info ! excellent!", "Gigante", "nebol@home.se", "Y");
insert into link(url, link_name, comment, name, email, verified) values ("http://members.tripod.com/~PGresh/nunchaku.htm", 	"Patrick Gresham's Nunchaku Page"														, "Great page, lots of techniques and videos", "Gigante", "nebol@home.se", "Y");
insert into link(url, link_name, comment, name, email, verified) values ("http://members.tripod.com/~Nunchaku/myself_e.htm", "Alex Levitas' Nunchaku Page"																, "Lots of info", "Gigante", "nebol@home.se", "Y");
insert into link(url, link_name, comment, name, email, verified) values ("http://www.rhythmarts.com/",												"Roy William's Rhythm Arts"																, "Lissajous Do Ryu ~ Nunchaku Weaponry System<br>An ugly webpage, but if you are patient you can learn and be amazed about their nunchaku system. ", "Gigante", "nebol@home.se", "Y");
insert into link(url, link_name, comment, name, email, verified) values ("http://users.cg.yu/ronin/", 												"Zeljko's Nunchaku Page - katas"														, "katas", "Gigante", "nebol@home.se", "Y");



insert into link(url, link_name, comment, name, email, verified) values ("http://m_i_n_a.tripod.com", "International Nunchaku Association - MINA", "", "Gigante", "nebol@home.se", "Y");
insert into link(url, link_name, comment, name, email, verified) values ("http://www.orcuttopn.com/", 												"Orcutt Police Defensive Systems - OPN III Police Nunchaku" , "Interesting", "Gigante", "nebol@home.se", "Y");
insert into link(url, link_name, comment, name, email, verified) values ("http://www.sundragonmartialarts.com/",							"SunDragon Martial Arts / American Style Nunchaku"					, "Video tapes and graduation in ASN", "Gigante", "nebol@home.se", "Y");
insert into link(url, link_name, comment, name, email, verified) values ("http://www.enterpriseguild.com/nunchaku/", 				"The Nunchaku Guild"																				, "Very nice page", "Gigante", "nebol@home.se", "Y");

insert into link(url, link_name, comment, name, email, verified) values ("http://martialarts.about.com/recreation/martialarts/library/weekly/aa091299.htm", "Nunchaku: Weapon as Movie Star", ""						, "Gigante", "nebol@home.se", "Y");			
insert into link(url, link_name, comment, name, email, verified) values ("http://www.nunchaku.org/", 												"World Nunchaku Association"																, "Nunchaku-Do", "Gigante", "nebol@home.se", "Y");


insert into link(url, link_name, comment, name, email, verified) values ("http://www.angelfire.com/nh/jessicakarate/nunchaku.html", "The Granite State Nunchaku Page"										, "Jessica's page, great videos", "Gigante", "nebol@home.se", "Y");


insert into link(url, link_name, comment, name, email, verified) values ("http://gladstone.uoregon.edu/~bpj31078/nform1.html","Nunchaku Basic Skills Form"																, "basic form"								, "Gigante", "nebol@home.se", "Y");
insert into link(url, link_name, comment, name, email, verified) values ("http://home.earthlink.net/~mikekirk/nunchuk.htm",	"Hawaiian Kenpo Karate Nunchaku form"												, "another form", "Gigante", "nebol@home.se", "Y");
insert into link(url, link_name, comment, name, email, verified) values ("http://www.uplandmacenter.com/jpnunart.html",			"Flashy moves"																							, "", "Gigante", "nebol@home.se", "Y");

insert into link(url, link_name, comment, name, email, verified) values ("http://nunchaku.8m.com/nunchaku.htm",							"Rons Nunchaku and Karate Site"															, "", "Gigante", "nebol@home.se", "Y");

-- Outdated
