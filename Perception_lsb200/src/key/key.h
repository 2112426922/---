/*
 * key.h
 *
 * created: 2024/3/5
 *  author: 
 */

#ifndef _KEY_H
#define _KEY_H

#include "ls1b_gpio.h"
#include "ls1b.h"

void KEY_IO_Config(void);
unsigned char KeyScan(void);

#endif // _KEY_H

