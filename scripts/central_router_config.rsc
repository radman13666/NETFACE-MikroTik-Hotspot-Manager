/system identity set name=Central_Router
/ip dhcp-client add interface=ether1 disabled=no use-peer-dns=yes \
use-peer-ntp=yes add-default-route=yes
/interface bridge add name=Central-Bridge disabled=no arp=enabled \
protocol-mode=stp
/interface ethernet set 2,3,4 master-port=ether2
/interface bridge port add interface=ether2 bridge=Central-Bridge
/ip address add address=<bridge addr w/ mask> interface=Central-Bridge
/ip pool add name=Central-Pool ranges=<pool range>
/ip dhcp-server add name=dhcp-srv-1 disabled=no address-pool=Central-Pool \
lease-time=3d interface=Central-Bridge
/ip dhcp-server network add address=<HS network w/ mask> gateway=<bridge addr w/o mask>
/routing ospf instance set default router-id=<bridge addr w/o mask> \
redistribute-connected=as-type-1 redistribute-other-ospf=as-type-1
/routing ospf network add network=<HS net w/ mask> area=backbone
/routing ospf network add network=<ISP net w/ mask> area=backbone
/ip firewall nat add action=dst-nat protocol=tcp dst-address=<ether1 addr w/o mask> \
dst-port=80 to-address=<web server addr w/o mask> to-port=80 chain=dstnat disabled=no
/user set 0 password=<user password>
