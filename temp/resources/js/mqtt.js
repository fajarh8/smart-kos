// import mqtt from "mqtt";
// const client = mqtt.connect("ws://192.168.1.99:8081", {
//     keepalive: 60,
// });
// client.on("connect", () => {
//     client.subscribe("presence", (err) => {
//         if (!err) {
//         client.publish("presence", "Hello mqtt");
//         }
//     });
// });

// client.on("message", (topic, message) => {
//     // message is Buffer
//     console.log(message.toString());
// });
