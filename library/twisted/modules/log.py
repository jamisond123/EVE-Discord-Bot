logfile = open("/tmp/discord_bot.db", "w")

def log(tbot, user, channel, msg):
	logfile.write("%s" % (msg))
	logfile.flush()
log.rule = ".*"
