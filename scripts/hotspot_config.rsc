/system identity set name=HS3
/ip dhcp-client add interface=ether1 add-default-route=yes disabled=no \
use-peer-dns=yes use-peer-ntp=yes
/interface bridge add name=loopback1 disabled=no arp=disabled \
protocol-mode=none
/ip address add address=10.0.0.3/32 interface=loopback1
/routing ospf instance set 0 router-id=10.0.0.3 \
redistribute-connected=as-type-1 redistribute-other-ospf=as-type-1
/routing ospf network add network=10.10.10.0/24 area=backbone
/ip service set 5 disabled=no
/user set 0 password=admin666
