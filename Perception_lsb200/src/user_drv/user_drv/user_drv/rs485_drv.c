/*
 * rs485_drv.c
 *
 * created: 2021/10/31
 *  author:
 */
#include "ns16550.h"
/*
 * UART3¿ØÖÆÆ÷³õÊ¼»¯
 */
 
void RS485_Init(void)
{
    unsigned int baud = 9600;
    ls1x_uart_init(devUART3, (void *)baud);
    ls1x_uart_open(devUART3, NULL);
}


