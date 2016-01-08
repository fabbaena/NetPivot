# Setting up Branches for Master, Develop and Local Environment
In order to have an environment for local testing, the environment for public
testing (**Develop**) and an environment for Stable Testing (**Master**), we
will follow the guidelines described in:

  http://nvie.com/posts/a-successful-git-branching-model/

## Process Flow
0. Git Environment, SSH Keys and Initial Repository Creation and Branching
1. Branching for **staging** and **develop** in local copy
2. Make changes
3. Commit changes
4. Push Changes to **origin staging**
5. **Preferred**: Make *pull requests* in `develop` or `master` branch
5. **Optional**: Get a *Temporal* **develop** or **master** branch local copy
6. **Optional**: Merge Changes from **origin/staging** or **origin/develop** in
   **No Fast Forward**
7. **Optional** Push Changes to **origin develop** or **origin master**

### Git Environment, SSH Keys and Initial Repository Creation
#### Git Environment
In order to setup the Developer Identity, it is needed to Configure the GIT
environment, just as described in
https://help.github.com/articles/set-up-git/

```sh
git config --global user.name "My complete Name"
git config --global user.email "My principal name set at GitHub account"
git config --global.core.editor "my favorite code editor"
```

#### SSH Keys
Then, you should create SSH keys for Server authentication, just as described
in https://help.github.com/articles/generating-ssh-keys/

TIP: There is no need to set a Key passphrase.

```sh
ssh-keygen -t rsa -b 4096 -C "your_email@example.com"

Generating public/private rsa key pair.
Enter file in which to save the key (/home/user/.ssh/id_rsa): /home/user/.ssh/github
Enter passphrase (empty for no passphrase):
Enter same passphrase again:
```

TIP: Configure the IdentityFile at ~/.ssh/config like the following example:

```
Host github
    CheckHostIP yes
    IdentityFile ~/.ssh/github
    PasswordAuthentication no
    PubkeyAuthentication yes
    RequestTTY auto
    User git
    HostName github.com
    VerifyHostKeyDNS yes
```

Then, you should add ~/.ssh/github.pub as Key in:
https://github.com/settings/ssh

Once added, you can test it with:
```sh
ssh -T git@github.com
```

#### Initial Repository Creation
If it is your first repository and first commit, create the repository first
on: https://github.com/new

Set a name (no spaces), a description (optional), and **do not** initialize it.
The repository will be complete empty, and without any branches.

The next step is to initialize the local repository, set the master branch and
push it.

You can initialize it with or without any source code inside the directory.

```sh
git init
git remote set-url origin git@github.com:User/Repository.git
git branch master
git checkout master
git add -v -A .
git status
git commit
git push origin master
```

To test that all is set as it should be, you can try downloading again:
```sh
git clone -b master git@github.com:User/Repository.git master/
```

#### Branching for **staging** and **develop** in local copy
Once the master branch is downloaded, you should create the **staging** and
**develop** branch:

```sh
git branch staging
git branch develop
git branch -vv
git checkout -f staging
```

Inside the new branch, copy or modify the code, stage it, commit it and push
it to the **staging** branch:

```sh
<COPY/MODIFY the code in the local repository directory>
git add -v -A .
git status
git commit -m "Initial commit in staging branch"
git push origin staging
```

TIP: In order to start cleanly after initial commit, you can delete the local
     repository and **clone** it again. It will download to latests changes.

### Get a Staging branch local copy
The local environment needs the following tools:
* `git` for Version Control
* Latest `apache` version
* Latest `php` version
* Latest `mariadb` version

Once done, you can clone the **staging** branch:

```sh
git clone -b staging git@github.com:SamanaGroup/NetPivot.git staging/
```

Once that is done, you can make changes and local commits.

```sh
git checkout staging
git add -v -A .
git status
git commit -a
```

### Push changes to Origin Staging
In order to update the local copy and then push the changes to the remote
**staging** branch:

```sh
git checkout staging
git pull
git push origin staging
git pull
```

### Get a temporal Develop or Master branch local copy
It is important to not commit changes to the **develop** or **master** branch, as it is only used for merging changes coming from **staging** or **develop** branch.

TIP: You should separate branches in diferent directories to
     avoid changes confusions.

```sh
git clone -b [develop|master] git@github.com:SamanaGroup/NetPivot.git [develop|master]/
```

### Merge changes from Origin/Staging or Origin/Develop in No Fast Forward
This step can be made on Web Dashboard using a pull request, going to:

* https://github.com/User/Repository/pull/new/develop
* https://github.com/User/Repository/pull/new/master

To merge changes that are already pushed to **origin/staging** branch
(described in Step 4):

```sh
git checkout develop
git pull
git merge --no-ff origin/staging
```

### Push Changes to Origin Develop or Origin Master
To push the already merged changes to Repository Server:

```sh
git push origin develop
```
The Stable Testing server will fetch the new changes from respective branch
eventually.

TIP: It is important to keep the **develop** or **master** local copy up to
date or cleanly clone it again.

## Public Testing Environment setup

