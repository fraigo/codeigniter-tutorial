BRANCH_NAME=$(git rev-parse --abbrev-ref HEAD)
UPSTREAMOK=$(git branch -v -a | grep remotes/upstream)
if [ "$UPSTREAMOK" == "" ]; then
    git remote add upstream git@github.com:fraigo/codeigniter-tutorial.git
    git fetch upstream
    git checkout --track upstream/tutorial
fi
git checkout tutorial && git pull && git add . && git commit -m "$1" && echo "Pushing $1 to upstream" && sleep 2 && git push && git checkout $BRANCH_NAME && git merge tutorial
