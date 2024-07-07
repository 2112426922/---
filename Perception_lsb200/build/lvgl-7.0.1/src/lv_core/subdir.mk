#
# Auto-Generated file. Do not edit!
#

# Add inputs and outputs from these tool invocations to the build variables
C_SRCS += \
../lvgl-7.0.1/src/lv_core/lv_debug.c \
../lvgl-7.0.1/src/lv_core/lv_disp.c \
../lvgl-7.0.1/src/lv_core/lv_group.c \
../lvgl-7.0.1/src/lv_core/lv_indev.c \
../lvgl-7.0.1/src/lv_core/lv_obj.c \
../lvgl-7.0.1/src/lv_core/lv_refr.c \
../lvgl-7.0.1/src/lv_core/lv_style.c

OBJS += \
./lvgl-7.0.1/src/lv_core/lv_debug.o \
./lvgl-7.0.1/src/lv_core/lv_disp.o \
./lvgl-7.0.1/src/lv_core/lv_group.o \
./lvgl-7.0.1/src/lv_core/lv_indev.o \
./lvgl-7.0.1/src/lv_core/lv_obj.o \
./lvgl-7.0.1/src/lv_core/lv_refr.o \
./lvgl-7.0.1/src/lv_core/lv_style.o

C_DEPS += \
./lvgl-7.0.1/src/lv_core/lv_debug.d \
./lvgl-7.0.1/src/lv_core/lv_disp.d \
./lvgl-7.0.1/src/lv_core/lv_group.d \
./lvgl-7.0.1/src/lv_core/lv_indev.d \
./lvgl-7.0.1/src/lv_core/lv_obj.d \
./lvgl-7.0.1/src/lv_core/lv_refr.d \
./lvgl-7.0.1/src/lv_core/lv_style.d

# Each subdirectory must supply rules for building sources it contributes
lvgl-7.0.1/src/lv_core/%.o: ../lvgl-7.0.1/src/lv_core/%.c
	@echo 'Building file: $<'
	@echo 'Invoking: SDE Lite C Compiler'
	D:/LoongIDE/mips-2011.03/bin/mips-sde-elf-gcc.exe -mips32 -G0 -EL -msoft-float -DLIB_LVGL -DLS1B -DOS_RTTHREAD  -O0 -g -Wall -c -fmessage-length=0 -pipe -I"../" -I"../include" -I"$(GCC_SPECS)/$(OS)/lvgl-7.0.1" -I"../ls1x-drv/include" -I"../RTThread/bsp-ls1x" -I"../RTThread/components/dfs/include" -I"../RTThread/components/drivers/include" -I"../RTThread/components/finsh" -I"../RTThread/components/libc/time" -I"../RTThread/include" -I"../RTThread/port/include" -I"../RTThread/port/mips" -I"../src/buzzer" -I"../src/gpio_uart" -I"../src/hdc2080" -I"../src/key" -I"../src/lvgl_ui" -I"../src/other" -I"../src/relay" -I"../src/SPL06_007" -I"../src/tsl2561fn" -I"../src/user_drv" -MMD -MP -MF"$(@:%.o=%.d)" -MT"$(@)" -o "$@" "$<"
	@echo 'Finished building: $<'
	@echo ' '

