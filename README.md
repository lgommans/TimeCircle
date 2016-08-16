# TimeCircle

Create circular timelapses.

![Circular timelapse](examples/timecircle1.gif)

Todo: fix that bug that you see in the gif above (the stripes). It's 2am and I
still got other stuff to do before sleeping. (I already wasted 30 minutes
throwing together the php script.)

## How to use

The tool is very crude and unix-like in that it does only one thing: convert
image sequences that contain a normal timelapse to another image sequence that
contains the circular timelapse. All the rest, like converting an image
sequence from/to a gif, has already been done by other projects like
imagemagick, and so you will need to use them.

A todo item is to detect availability of these tools and integrate with them.
For now, use it like this:

    # Convert an existing timelapse gif into a sequence of images.
	# Note that the convert command is part of imagemagick.
    convert -coalesce your.gif %d.jpg

    # Use the tool to create a circular timelapse.
    ./timecircle.php {0..79}.jpg

    # And finally convert the resulting sequence to a gif again.
	# A lower delay is a quicker gif, by the way.
    convert -delay 9 timecircle/{0..79}.jpg timecircle.gif

