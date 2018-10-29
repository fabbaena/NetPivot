# NetPivot Development Environment install

Requirements:
- Ubuntu 14.04 LTS
- Disk space 100MB

```bash
sudo apt update
sudo apt install -y git
cat <<EOF > ~/.ssh/id_rsa
# Replace this text with ssh private key
EOF
chmod 600 ~/.ssh/id_rsa

git clone -b develop git@github.com:/fabbaena/NetPivot

NetPivot/scripts/install_develop.sh
```