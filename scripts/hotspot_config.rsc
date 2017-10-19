/system identity set name=HSn
/ip dhcp-client add interface=ether1 add-default-route=yes disabled=no \
use-peer-dns=yes use-peer-ntp=yes
/interface bridge add name=loopback1 disabled=no arp=disabled \
protocol-mode=none
/ip address add address=<loopback addr w/ mask> interface=loopback1
/routing ospf instance set 0 router-id=<loopback addr w/o mask> \
redistribute-connected=as-type-1 redistribute-other-ospf=as-type-1
/routing ospf network add network=<HS net w/ mask> area=backbone
/ip service set 5 disabled=no
/user set 0 password=<user password>
