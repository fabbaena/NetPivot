# NetPivot Development Environment install

Requirements:
* Ubuntu 14.04 LTS
* Disk space 100MB

sudo apt update
sudo apt install -y git
cat <<EOF > ~/.ssh/id_rsa
<put you private key in here>
EOF
chmod 600 ~/.ssh/id_rsa

git clone -b develop git@github.com:/fabbaena/NetPivot

NetPivot/scripts/install_develop.sh
