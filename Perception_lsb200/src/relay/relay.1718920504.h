/*
 * relay.h
 *
 * created: 2024/3/23
 *  author: 
 */

#ifndef _RELAY_H
#define _RELAY_H

#include "ns16550.h"

#define RELAY_ON    1
#define RELAY_OFF   0

#define RELAY_FLASH_OFF    0x04     //闪闭模式
#define RELAY_FLAHS_ON     0x02     //闪断模式

void UART3_Config_Init(void);
void relay_on_off(unsigned char num, unsigned char mode);

#endif // _RELAY_H

