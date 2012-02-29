phpcsfork () {
	phpcs -v --standard="/Applications/MAMP/htdocs/forkcms/tools/codesniffer/Fork" --extensions=php --report=full "${@:-..}";
}