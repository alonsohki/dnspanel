#include <errno.h>
#include <stdio.h>
#include <stdlib.h>

int main()
{
  if (setreuid(0, 0))
  {
    perror("Error changing to uid 0");
    return EXIT_FAILURE;
  }

  return system("/usr/sbin/rndc -k /etc/bind/rndc.key reload");
}
