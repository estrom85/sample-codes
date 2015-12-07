<?php
class UserContract{
	const TABLE = "user";
	const _ID = "user_id";
	const COL_NAME = "user_name";
	const COL_PASSWORD = "user_password";
	const COL_USER_TYPE = "type_id";
}

class HomeContract{
	const TABLE = "home";
	const _ID = "home_id";
	const COL_USER_ID = "user_id";
	const COL_NAME = "home_name";
}

class ZoneContract{
	const TABLE = "zone";
	const _ID = "zone_id";
	const COL_HOME_ID = "home_id";
	const COL_NAME = "name";
}

class RoomContract{
	const TABLE = "room";
	const _ID = "room_id";
	const COL_HOME_ID = "home_id";
	const COL_ZONE_ID = "zone_id";
	const COL_NAME = "name";
}

class RoomZoneContract{
	const TABLE = "room_zone";
	const _ID = "room_zone_id";
	const FK_ROOM_ID = "room_id";
	const FK_ZONE_ID = "zone_id";
}