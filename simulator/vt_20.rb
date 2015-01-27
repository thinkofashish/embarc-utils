require 'socket'
#require 'csv'
require 'nokogiri'

hostname = 'mycanvasback.com'
port = 21000

count = 1

# parse GPX file using Nokogiri - meaning handsaw
gpx_file = Nokogiri::XML(File.open('data.gpx'))
trkpts = gpx_file.css('xmlns|trkpt')
while true do
	speed = 0
	trkpts.each do |trkpt|		
		# current time in UTC
		time = Time.now.utc.strftime "%Y%m%d%H%M%S"
		
		# location from file
		latitude = trkpt.xpath('@lat').to_s.to_f
		longitude = trkpt.xpath('@lon').to_s.to_f

		# data packet
		some_data = "2000000011,#{time},#{longitude},#{latitude},#{speed},25,0,8,0,7,2,0.50,0.60,0,0"
		
		send_data(some_data)
		
		# debug
		puts "#{count} sent at #{time}"
		
		count += 1
		
		speed += 1
		
		# reset speed to 0, if speed exceeds 150kmph
		if speed > 150
			speed = 0
		end
		
		# wait time
		sleep 1
	end
end

def send_data(data)
	# open a socket connection to the server
	sock = TCPSocket.open(hostname, port)
		
	# send some data
	sock.print(data)
		
	# close the connection
	sock.close
end
=begin
# reading a CSV file
CSV.foreach("data_2.csv") do |row|	
	# current time in UTC
	time = Time.now.utc.strftime "%Y%m%d%H%M%S"
	
	# location from file
	latitude = row[0]
	longitude = row[1]
	
	# data packet
	some_data = "2000000011,#{time},#{longitude},#{latitude},42,25,0,8,0,7,2,0.50,0.60,0,0"
	
	# open a socket connection to the server
	sock = TCPSocket.open(hostname, port)
	
	# send some data
	sock.print(some_data)
	
	# close the connection
	sock.close
	
	# debug
	puts "#{count} sent at #{time}"
	
	# wait time
	sleep 1
end
=end