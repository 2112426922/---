#
# Auto-Generated file. Do not edit!
#

# Add inputs and outputs from these tool invocations to the build variables
C_SRCS += \
../lvgl-7.0.1/src/lv_draw/lv_draw_arc.c \
../lvgl-7.0.1/src/lv_draw/lv_draw_blend.c \
../lvgl-7.0.1/src/lv_draw/lv_draw_img.c \
../lvgl-7.0.1/src/lv_draw/lv_draw_label.c \
../lvgl-7.0.1/src/lv_draw/lv_draw_line.c \
../lvgl-7.0.1/src/lv_draw/lv_draw_mask.c \
../lvgl-7.0.1/src/lv_draw/lv_draw_rect.c \
../lvgl-7.0.1/src/lv_draw/lv_draw_triangle.c \
../lvgl-7.0.1/src/lv_draw/lv_img_buf.c \
../lvgl-7.0.1/src/lv_draw/lv_img_cache.c \
../lvgl-7.0.1/src/lv_draw/lv_img_decoder.c

OBJS += \
./lvgl-7.0.1/src/lv_draw/lv_draw_arc.o \
./lvgl-7.0.1/src/lv_draw/lv_draw_blend.o \
./lvgl-7.0.1/src/lv_draw/lv_draw_img.o \
./lvgl-7.0.1/src/lv_draw/lv_draw_label.o \
./lvgl-7.0.1/src/lv_draw/lv_draw_line.o \
./lvgl-7.0.1/src/lv_draw/lv_draw_mask.o \
./lvgl-7.0.1/src/lv_draw/lv_draw_rect.o \
./lvgl-7.0.1/src/lv_draw/lv_draw_triangle.o \
./lvgl-7.0.1/src/lv_draw/lv_img_buf.o \
./lvgl-7.0.1/src/lv_draw/lv_img_cache.o \
./lvgl-7.0.1/src/lv_draw/lv_img_decoder.o

C_DEPS += \
./lvgl-7.0.1/src/lv_draw/lv_draw_arc.d \
./lvgl-7.0.1/src/lv_draw/lv_draw_blend.d \
./lvgl-7.0.1/src/lv_draw/lv_draw_img.d \
./lvgl-7.0.1/src/lv_draw/lv_draw_label.d \
./lvgl-7.0.1/src/lv_draw/lv_draw_line.d \
./lvgl-7.0.1/src/lv_draw/lv_draw_mask.d \
./lvgl-7.0.1/src/lv_draw/lv_draw_rect.d \
./lvgl-7.0.1/src/lv_draw/lv_draw_triangle.d \
./lvgl-7.0.1/src/lv_draw/lv_img_buf.d \
./lvgl-7.0.1/src/lv_draw/lv_img_cache.d \
./lvgl-7.0.1/src/lv_draw/lv_img_decoder.d

# Each subdirectory must supply rules for building sources it contributes
lvgl-7.0.1/src/lv_draw/%.o: ../lvgl-7.0.1/src/lv_draw/%.c
	@echo 'Building file: $<'
	@echo 'Invoking: SDE Lite C Compiler'
	D:/LoongIDE/mips-2011.03/bin/mips-sde-elf-gcc.exe -mips32 -G0 -EL -msoft-float -DLIB_LVGL -DLS1B -DOS_RTTHREAD  -O0 -g -Wall -c -fmessage-length=0 -pipe -I"../" -I"../include" -I"$(GCC_SPECS)/$(OS)/lvgl-7.0.1" -I"../ls1x-drv/include" -I"../RTThread/bsp-ls1x" -I"../RTThread/components/dfs/include" -I"../RTThread/components/drivers/include" -I"../RTThread/components/finsh" -I"../RTThread/components/libc/time" -I"../RTThread/include" -I"../RTThread/port/include" -I"../RTThread/port/mips" -I"../src/buzzer" -I"../src/gpio_uart" -I"../src/hdc2080" -I"../src/key" -I"../src/lvgl_ui" -I"../src/other" -I"../src/relay" -I"../src/SPL06_007" -I"../src/tsl2561fn" -I"../src/user_drv" -MMD -MP -MF"$(@:%.o=%.d)" -MT"$(@)" -o "$@" "$<"
	@echo 'Finished building: $<'
	@echo ' '

