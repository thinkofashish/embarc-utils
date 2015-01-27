/*
* Execute bash script as root user
* 
* Note: Apply appropriate permissions to the compiled file of this code
* chown root:root <filename>
* chmod 4755 <filename>
*
* Compilation Instructions:
* gcc runscript.c -o <filename>
*/

#include <stdio.h>
#include <stdlib.h>
#include <sys/types.h>
#include <unistd.h>
#include <string.h>

int main(int argc, char *argv[])
{
	setuid( 0 );
	char str[100];
	if(strcmp(argv[1], "c") == 0) { // (c) - complete file
		sprintf(str, "cat %s", argv[3]);
	} else { // (p) - part from file
		sprintf(str, "tail -n%s %s", argv[2], argv[3]);
	}
	system( str );

	return 0;
}
