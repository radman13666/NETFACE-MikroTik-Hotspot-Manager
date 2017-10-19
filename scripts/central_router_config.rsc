/system identity set name=Central_Router
/ip dhcp-client add interface=ether1 disabled=no use-peer-dns=yes \
use-peer-ntp=yes add-default-route=yes
/interface bridge add name=Central-Bridge disabled=no arp=enabled \
protocol-mode=stp
/interface ethernet set 2,3,4 master-port=ether2
/interface bridge port add interface=ether2 bridge=Central-Bridge
/ip address add address=10.10.10.1/24 interface=Central-Bridge
/ip pool add name=Central-Pool ranges=10.10.10.10-10.10.10.254
/ip dhcp-server add name=dhcp-srv-1 disabled=no address-pool=Central-Pool \
lease-time=3d interface=Central-Bridge
/ip dhcp-server network add address=10.10.10.0/24 gateway=10.10.10.1
/routing ospf instance set default router-id=10.10.10.1 \
redistribute-connected=as-type-1 redistribute-other-ospf=as-type-1
/routing ospf network add network=10.10.10.0/24 area=backbone
/routing ospf network add network=10.120.0.0/20 area=backbone
/ip firewall nat add action=dst-nat protocol=tcp dst-address=10.120.6.206 \
dst-port=80 to-address=10.10.10.249 to-port=80 chain=dstnat disabled=no
/user set 0 password=admin666
