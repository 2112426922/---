/**
 * @file lv_port_fs.h
 *
 */

#include "bsp.h"

#ifdef USE_LVGL

#ifndef LV_PORT_FS_H
#define LV_PORT_FS_H

#ifdef __cplusplus
extern "C" {
#endif

//-------------------------------------------------------------------------------------------------
// INCLUDES
//-------------------------------------------------------------------------------------------------

#ifndef LIB_LVGL
#include "../lvgl.h"
#else
#include "lvgl.h"
#endif

//-------------------------------------------------------------------------------------------------
// DEFINES
//-------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
// TYPEDEFS
//-------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
// GLOBAL PROTOTYPES
//-------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
// MACROS
//-------------------------------------------------------------------------------------------------

#ifdef __cplusplus
} /* extern "C" */
#endif

#endif /* LV_PORT_FS_H */

#endif /* #ifdef USE_LVGL */



