# NetPivot Development Environment install

Requirements:
- Ubuntu 14.04 LTS
- Disk space 100MB

The following commands need to be run in the shell with a user with sudo privileges
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

After this is done, NetPivot should be available through the browser pointing to the IP address of the server.

Source code for development will be stored in `/usr/src/develop`.
*Please use a new branch.*
After code has been updated, use git to commit and update